<?php

namespace App\Services\Auth;
use App\DTOs\RegisterDTO;
use App\DTOs\VerifyPhoneDTO;
use App\DTOs\LoginDTO;
use App\DTOs\ResetPassowrdDTO;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Carbon\CarbonInterval;
class UserAuthService
{
    public function __construct(private TokenService $tokenService, private OtpService $otpService, private \App\Services\CloudImageService $cis)
    {
    }
    public function register(RegisterDTo $dto)
    {
        $this->isAlreadyExist($dto->phone);
        DB::transaction(function () use ($dto) {
            $user = User::create([
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'password' => bcrypt($dto->password),
            ]);
            $this->otpService->sendOTP($dto->phone);
            if ($dto->image) {
                $imageData = $this->cis->upload($dto->image);
                $user->image()->create([
                    'image_url' => $imageData['url'],
                    'public_id' => $imageData['public_id']
                ]);
            }
        });
    }

    public function isAlreadyExist(string $phone)
    {
        if (User::where('phone', $phone)->exists()) {
            throw new \App\Exceptions\UserAlreadyExistsException();
        }
    }

    public function verifyPhone(VerifyPhoneDTO $dto)
    {
        $user = $this->isFoundUser($dto->phone);
        if ($user->is_verified) {
            throw new \App\Exceptions\AlreadyVerifiedException();
        }
        $otp = $this->otpService->verify($dto->phone, $dto->code);
        return DB::transaction(function () use ($user, $otp) {
            $user->is_verified = true;
            $user->save();
            $otp->delete();
            return [
                'user' => $user,
                ...$this->tokenService->issueTokens($user)
            ];
        });


    }

    public function verifyPhoneForResetPassword(VerifyPhoneDTO $dto)
    {
        $this->isFoundUser($dto->phone);
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

    public function login(LoginDTO $dto)
    {
        $user = $this->isFoundUser($dto->phone);
        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new \App\Exceptions\InvalidCredentialsException();
        }
        if (!$user->is_verified) {
            throw new \App\Exceptions\UserNotVerifiedException();
        }
        return [
            'user' => $user,
            ...$this->tokenService->issueTokens($user)
        ];
    }

    public function isFoundUser(string $phone)
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        return $user;
    }

    public function resetPassword(ResetPassowrdDTO $dto)
    {
        $user = $this->isFoundUser($dto->phone);
        $resetToken = Cache::get($dto->phone);
        if (!$resetToken || $resetToken !== $dto->resetToken) {
            throw new \App\Exceptions\FailedResetPasswordException();
        }
        $user->password = Hash::make($dto->newPassword);
        $user->save();
        Cache::forget($dto->phone);
    }

    public function sendOtpCode(string $phone)
    {
        $this->isFoundUser($phone);
        $this->otpService->sendOTP($phone);
    }

    public function logout(User $user)
    {
        $this->tokenService->revokeTokens($user);
    }
    public function refreshTokens(string $refreshToken)
    {
        return $this->tokenService->rotateRefreshToken($refreshToken);
    }
    public function addAddress(\App\DTOs\AddressDTO $dto, User $user)
    {
        $address = $user->address->updateOrCreate([
        ], $dto->toArray());
        return $address;
    }

    public function updateAddress(\App\DTOs\AddressDTO $dto, User $user)
    {
        $address = $user->address;
        if (!$address) {
            throw new \App\Exceptions\NotFoundAddressException();
        }
        $address->update($dto->toArray());
        return $address;
    }
}
