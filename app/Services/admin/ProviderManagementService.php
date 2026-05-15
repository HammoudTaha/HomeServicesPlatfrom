<?php

namespace App\Services\admin;
use App\DTOs\CreateProviderDTO;
use App\Models\Provider;
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
            if (Provider::where('phone', $dto->phone)->exists()) {
                throw new \App\Exceptions\UserAlreadyExistsException();
            }
            $provider = Provider::create([
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'service_category_id' => $dto->serviceCategoryId,
                'address' => $dto->address,
                'experience_years' => $dto->experienceYears
            ]);
            $provider->wallet()->create([
            ]);
            return $provider;
        });
        $provider->refresh();
        return $provider;
    }
    public function getAllProviders()
    {
        return Provider::with(['serviceCategory', 'image'])->get();
    }
    public function getProviderById(int $id)
    {
        $provider = $this->isFoundProvider($id);
        return $provider->load(['serviceCategory', 'image']);
    }

    public function deleteProvider(int $id)
    {
        $provider = $this->isFoundProvider($id);
        $provider->delete();
    }
    public function activateProvider(int $id)
    {
        $provider = $this->isFoundProvider($id);
        $provider->update(['is_active' => true]);
    }

    public function deactivateProvider(int $id)
    {
        $provider = $this->isFoundProvider($id);
        $provider->update(['is_active' => false]);
    }

    public function searchProviders(string $query)
    {
        return Provider::where('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->get();
    }

    private function isFoundProvider(string $id)
    {
        $provider = Provider::where('id', $id)->first();
        if (!$provider) {
            throw new \App\Exceptions\NotFoundUserException();
        }
        return $provider;
    }

}
