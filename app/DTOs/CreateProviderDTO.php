<?php

namespace App\DTOs;
use App\Http\Requests\Admin\CreateProviderRequest;
class CreateProviderDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $firstName, public string $lastName, public ?string $email, public string $phone, public string $address, public int $experienceYears, public int $serviceCategoryId)
    {
    }
    public static function fromRequest(CreateProviderRequest $request): self
    {
        return new self(
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            address: $request->input('address'),
            experienceYears: $request->input('experience_years'),
            serviceCategoryId: $request->input('service_category_id'),
        );
    }
}
