<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'street' => $this->street,
            'city' => $this->city,
            'area' => $this->area,
            'country' => $this->country,
            'building' => $this->building,
            'floor' => $this->floor,
            'apartment' => $this->apartment,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'display_address' => $this->address(),
        ];
    }
    public function address(): string
    {
        if ($this->apartment) {
            return $this->apartment . ', ' . $this->floor . ', ' . $this->building . ', ' . $this->street . ', ' . $this->area . ', ' . $this->city . ', ' . $this->country;
        } else if ($this->street) {
            return $this->street . ', ' . $this->area . ', ' . $this->city . ', ' . $this->country;
        } else {
            return $this->area . ', ' . $this->city . ', ' . $this->country;
        }
    }
}
