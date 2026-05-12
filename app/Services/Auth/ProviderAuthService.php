<?php
namespace App\Services\Auth;

use App\DTOs\LoginDTO;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Carbon\CarbonInterval;
use App\DTOs\VerifyPhoneDTO;
use App\DTOs\ResetPassowrdDTO;
class ProviderAuthService
{
    public function __construct(private TokenService $tokenService, private OtpService $otpService)
    {
    }
    public function login(LoginDTO $dto)
    {
        $provider = Provider::where('phone', $dto->phone)->first();
        if (!$provider || !Hash::check($dto->password, $provider->password)) {
            throw new \App\Exceptions\InvalidCredentialsException();
        }
        if (!$provider->is_active) {
            throw new \App\Exceptions\UserNotActiveException();
        }
        return $this->tokenService->issueTokens($provider);
    }

    public function logout(Provider $provider)
    {
        $this->tokenService->revokeTokens($provider);
    }
    public function refreshTokens(string $refreshToken)
    {
        return $this->tokenService->rotateRefreshToken($refreshToken);
    }

    public function sendOtpCode(string $phone)
    {
        $provider = Provider::where('phone', $phone)->first();
        if (!$provider) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        $this->otpService->sendOTP($phone);
    }

    public function isFoundProvider(string $phone)
    {
        $provider = Provider::where('phone', $phone)->first();
        if (!$provider) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        return $provider;
    }

    public function verifyPhoneForResetPassword(VerifyPhoneDTO $dto)
    {
        $this->isFoundProvider($dto->phone);
        if ($this->otpService->verify($dto->phone, $dto->code)) {
            $resetToken = Str::random(20);
            $expiration = config('app.otp_expiration', 5);
            $expirationText = CarbonInterval::minutes($expiration)->cascade()->forHumans();
            Cache::put($dto->phone, $resetToken, now()->addMinutes($expiration));
            return ['token' => $resetToken, 'expires_in' => $expirationText];
        }
    }

    public function resetPassword(ResetPassowrdDTO $dto)
    {
        $provider = $this->isFoundProvider($dto->phone);
        $resetToken = Cache::get($dto->phone);
        if (!$resetToken || $resetToken !== $dto->resetToken) {
            throw new \App\Exceptions\FailedResetPasswordException();
        }
        $provider->password = Hash::make($dto->newPassword);
        $provider->save();
        Cache::forget($dto->phone);
    }

}