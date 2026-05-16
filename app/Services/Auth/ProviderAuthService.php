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
use App\DTOs\CreateProviderDTO;
use App\Models\ServiceCategory;

class ProviderAuthService
{
    public function __construct(private TokenService $tokenService, private OtpService $otpService, private CloudImageService $cis)
    {
    }
    public function createProvider(CreateProviderDTO $dto)
    {
        $provider = DB::transaction(function () use ($dto) {
            if (Provider::where('phone', $dto->phone)->exists()) {
                throw new \App\Exceptions\UserAlreadyExistsException();
            }
            $provider = Provider::create([
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'service_category_id' => $dto->serviceCategoryId,
                'experience_years' => $dto->experienceYears
            ]);
            $provider->wallet()->create([
            ]);
            return $provider;
        });
        $provider->refresh();
        return $provider;
    }
    public function login(LoginDTO $dto)
    {
        $provider = $this->isPhoneProviderFound($dto->phone);
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
        $provider = $this->isPhoneProviderFound($phone);
        $this->isActiveProvider($provider);
        $this->otpService->sendOTP($phone);
    }
    private function isPhoneProviderFound(string $phone)
    {
        $provider = Provider::where('phone', $phone)->first();
        if (!$provider) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        return $provider;
    }
    private function isIdProviderFound(string $id)
    {
        $provider = Provider::where('id', $id)->first();
        if (!$provider) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        return $provider;
    }
    public function verifyPhoneForResetPassword(VerifyPhoneDTO $dto)
    {
        $provider = $this->isPhoneProviderFound($dto->phone);
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
        $provider = $this->isPhoneProviderFound($dto->phone);
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
    public function getAllProviders()
    {
        return Provider::with(['serviceCategory', 'image'])->get();
    }
    public function getProviderById(int $id)
    {
        $provider = $this->isIdProviderFound($id);
        return $provider->load(['serviceCategory', 'image']);
    }
    public function deleteProvider(int $id)
    {
        $provider = $this->isIdProviderFound($id);
        $provider->delete();
    }
    public function activateProvider(int $id)
    {
        $provider = $this->isIdProviderFound($id);
        $provider->update(['is_active' => true]);
    }
    public function deactivateProvider(int $id)
    {
        $provider = $this->isIdProviderFound($id);
        $provider->update(['is_active' => false]);
    }
    public function searchProviders(string $query)
    {
        return Provider::where('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->get();
    }
    public function getProvidersByCategory(int $categoryId)
    {
        $category = ServiceCategory::find($categoryId);
        if (!$category) {
            throw new \App\Exceptions\FaildCategoryProcessException(message: 'Category not found');
        }
        return $category->providers()->where('is_active', true)->with('image')->get();
    }
    public function getProviderDetails(int $id)
    {
        $provider = $this->isIdProviderFound($id);
        if (!$provider->is_active) {
            throw new \App\Exceptions\UserNotActiveException(message: 'Provider is not active', code: 400);
        }
        return $provider->load(['serviceCategory', 'image', 'reviews', 'address', 'services']);
    }

    public function addAddress(\App\DTOs\AddressDTO $dto, Provider $provider)
    {
        $address = $provider->address->updateOrCreate([
        ], $dto->toArray());
        return $address;
    }

    public function updateAddress(\App\DTOs\AddressDTO $dto, Provider $provider)
    {
        $address = $provider->address;
        if (!$address) {
            throw new \App\Exceptions\NotFoundAddressException();
        }
        $address->update($dto->toArray());
        return $address;
    }
}