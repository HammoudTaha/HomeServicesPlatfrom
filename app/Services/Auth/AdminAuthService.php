<?php

namespace App\Services\Auth;
use App\DTOs\LoginDTO;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Carbon\CarbonInterval;
use App\DTOs\VerifyLoginDTO;
use App\Services\Auth\TokenService;
use App\Services\Auth\OtpService;
use Illuminate\Support\Facades\DB;
class AdminAuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private TokenService $tokenService, private OtpService $otpService)
    {
    }
    public function login(LoginDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $admin = $this->isFoundAdmin($dto->phone);
            if (!Hash::check($dto->password, $admin->password)) {
                throw new \App\Exceptions\InvalidCredentialsException();
            }
            $resetToken = Str::random(20);
            $expiration = config('app.otp_expiration', 5);
            $expirationText = CarbonInterval::minutes((int) $expiration)->cascade()->forHumans();
            Cache::put($dto->phone, $resetToken, now()->addMinutes((int) $expiration));
            $this->otpService->sendOTP($dto->phone);
            return ['login_token' => $resetToken, 'expires_in' => $expirationText];
        });
    }
    public function verifyLogin(VerifyLoginDTO $dto)
    {
        $admin = $this->isFoundAdmin($dto->phone);
        $otp = $this->otpService->verify($dto->phone, $dto->code);
        return DB::transaction(function () use ($admin, $dto, $otp) {
            $resetToken = Cache::get($dto->phone);
            if (!$resetToken || $resetToken !== $dto->loginToken) {
                throw new \App\Exceptions\FailedVerifyPhoneException('failed to verify login token .invalid or expired login token');
            }
            Cache::forget($dto->phone);
            $otp->delete();
            return [
                'admin' => $admin,
                ...$this->tokenService->issueTokens($admin)
            ];
        });

    }

    public function changePassword(\App\DTOs\ChangePasswordDTO $dto)
    {
        if (!Hash::check($dto->currentPassword, $dto->admin->password)) {
            throw new \App\Exceptions\InvalidCredentialsException(message: 'Current password is incorrect');
        }
        $dto->admin->update([
            'password' => Hash::make($dto->newPassword),
        ]);
    }

    public function logout(Admin $admin)
    {
        $this->tokenService->revokeTokens($admin);
    }



    public function refreshTokens(string $refreshToken)
    {
        return $this->tokenService->rotateRefreshToken($refreshToken);
    }

    private function isFoundAdmin(string $phone)
    {
        $admin = Admin::where('phone', $phone)->first();
        if (!$admin) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        return $admin;
    }
}
