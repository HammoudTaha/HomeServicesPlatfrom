<?php

namespace App\DTOs;

class AddressDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public ?string $city,
        public ?string $area,
        public ?string $street,
        public ?string $state,
        public ?string $building,
        public ?string $floor,
        public ?string $apartment,
        public ?float $latitude,
        public ?float $longitude
    ) {
    }
    public static function fromRequest(\App\Http\Requests\Auth\AddAddressRequest|\App\Http\Requests\Auth\UpdateAddressRequest $request): self
    {
        return new self(
            city: $request->input('city'),
            area: $request->input('area'),
            street: $request->input('street'),
            state: $request->input('state'),
            building: $request->input('building'),
            floor: $request->input('floor'),
            apartment: $request->input('apartment'),
            latitude: $request->input('latitude'),
            longitude: $request->input('longitude')
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'country' => 'Syria',
            'city' => $this->city,
            'area' => $this->area,
            'street' => $this->street,
            'state' => $this->state,
            'building' => $this->building,
            'floor' => $this->floor,
            'apartment' => $this->apartment,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ], function ($value) {
            return !is_null($value);
        });
    }
}
