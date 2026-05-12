<?php

namespace App\Services\admin;
use App\DTOs\CreateProviderDTO;
use App\Models\Provider;
use App\Models\ProviderWallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class ProviderManagementService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    public function createProvider(CreateProviderDTO $dto)
    {
        $provider = DB::transaction(function () use ($dto) {
            $provider = Provider::create([
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'password' => Hash::make($dto->password),
                'service_category_id' => $dto->serviceCategoryId,
                'address' => $dto->address,
                'experience_years' => $dto->experienceYears
            ]);
            $provider->wallet()->create([
                'balance' => 0,
            ]);
            return $provider;
        });
        return [
            'provider' => $provider,
        ];
    }
    public function getAllProviders()
    {
        return Provider::with('serviceCategory')->get();
    }
    public function getProviderById(int $id)
    {
        return Provider::with('serviceCategory')->findOrFail($id);
    }
    public function updateProvider(\App\DTOs\UpdateProviderDTO $dto)
    {
        $provider = Provider::findOrFail($dto->providerId);
        $provider->update([
            'first_name' => $dto->firstName ?? $provider->first_name,
            'last_name' => $dto->lastName ?? $provider->last_name,
            'email' => $dto->email ?? $provider->email,
            'password' => isset($dto->password) ? Hash::make($dto->password) : $provider->password,
            'service_category_id' => $dto->serviceCategoryId ?? $provider->service_category_id,
            'address' => $dto->address ?? $provider->address,
            'experience_years' => $dto->experienceYears ?? $provider->experience_years
        ]);
    }

    public function deleteProvider(int $id)
    {
        $provider = Provider::findOrFail($id);
        $provider->delete();
    }
    public function activateProvider(int $id)
    {
        $provider = Provider::findOrFail($id);
        $provider->update(['is_active' => true]);
    }

    public function deactivateProvider(int $id)
    {
        $provider = Provider::findOrFail($id);
        $provider->update(['is_active' => false]);
    }

    public function searchProviders(string $query)
    {
        return Provider::where('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->get();
    }

}
