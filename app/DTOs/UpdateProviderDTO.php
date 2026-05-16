<?php

namespace App\DTOs;
use Illuminate\Http\UploadedFile;

class UpdateProviderDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $firstName, public ?string $lastName, public ?string $email, public ?string $address, public ?int $experienceYears, public ?string $description, public ?UploadedFile $image)
    {
    }
    public static function fromRequest(\App\Http\Requests\Provider\UpdateProviderRequest $request): self
    {
        return new self(
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
            email: $request->input('email'),
            address: $request->input('address'),
            experienceYears: $request->input('experience_years'),
            description: $request->input('description'),
            image: $request->file('image'),
        );
    }
    public function toArray(): array
    {
        return array_filter([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'address' => $this->address,
            'experience_years' => $this->experienceYears,
            'description' => $this->description,
        ], fn($value) => !is_null($value));
    }
}
