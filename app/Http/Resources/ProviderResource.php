<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'service_category_id' => $this->serviceCategory?->id,
            'service_category_name' => $this->serviceCategory?->name,
            'address' => $this->address,
            'experience_years' => $this->experience_years,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'image_url' => $this->image?->image_url,
            'rating' => $this->rating,
            'rating_count' => $this->rating_count,
            'is_available' => $this->is_available,
            'created_at' => $this->created_at,
        ];
    }
}
