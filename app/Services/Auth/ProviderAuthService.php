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
use Illuminate\Support\Facades\DB;
use App\DTOs\UpdateProviderDTO;
use App\Services\CloudImageService;
class ProviderAuthService
{
    public function __construct(private TokenService $tokenService, private OtpService $otpService, private CloudImageService $cis)
    {
    }
    public function login(LoginDTO $dto)
    {
        $provider = $this->isFoundProvider($dto->phone);
        if (!$provider->isSetPassword()) {
            throw new \App\Exceptions\InvalidCredentialsException(message: 'Password is not set for this provider. Please set your password first.');
        }
        if (!Hash::check($dto->password, $provider->password)) {
            throw new \App\Exceptions\InvalidCredentialsException();
        }
        $this->isActiveProvider($provider);
        return $this->tokenService->issueTokens($provider);
    }

    private function isActiveProvider(Provider $provider)
    {
        if (!$provider->is_active) {
            throw new \App\Exceptions\UserNotActiveException();
        }
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
        $provider = $this->isFoundProvider($phone);
        $this->isActiveProvider($provider);
        $this->otpService->sendOTP($phone);
    }

    private function isFoundProvider(string $phone)
    {
        $provider = Provider::where('phone', $phone)->first();
        if (!$provider) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        return $provider;
    }

    public function verifyPhoneForResetPassword(VerifyPhoneDTO $dto)
    {
        $provider = $this->isFoundProvider($dto->phone);
        $this->isActiveProvider($provider);
        $otp = $this->otpService->verify($dto->phone, $dto->code);
        return DB::transaction(function () use ($dto, $otp) {
            $resetToken = Str::random(20);
            $expiration = config('app.otp_expiration', 5);
            $expirationText = CarbonInterval::minutes((int) $expiration)->cascade()->forHumans();
            Cache::put($dto->phone, $resetToken, now()->addMinutes((int) $expiration));
            $otp->delete();
            return ['token' => $resetToken, 'expires_in' => $expirationText];
        });
    }

    public function resetPassword(ResetPassowrdDTO $dto)
    {
        $provider = $this->isFoundProvider($dto->phone);
        $this->isActiveProvider($provider);
        $resetToken = Cache::get($dto->phone);
        if (!$resetToken || $resetToken !== $dto->resetToken) {
            throw new \App\Exceptions\FailedResetPasswordException();
        }
        $provider->password = Hash::make($dto->newPassword);
        $provider->save();
        Cache::forget($dto->phone);
    }

    public function updateProfile(UpdateProviderDTO $dto, Provider $provider)
    {
        $provider->update($dto->toArray());
        if ($dto->image) {
            $oldImage = $provider->image()->first();
            if ($oldImage) {
                $imageData = $this->cis->update($dto->image, $oldImage->public_id);
                $provider->image()->update([
                    'image_url' => $imageData['url'],
                    'public_id' => $imageData['public_id']
                ]);
            } else {
                $imageData = $this->cis->upload($dto->image);
                $provider->image()->create([
                    'image_url' => $imageData['url'],
                    'public_id' => $imageData['public_id']
                ]);
            }
        }
        return $provider->load('image');
    }

    public function setAvailable(Provider $provider)
    {
        $provider->update([
            'is_available' => true
        ]);
        return $provider;
    }
    public function setUnavailable(Provider $provider)
    {
        $provider->update([
            'is_available' => false
        ]);
        return $provider;
    }



}