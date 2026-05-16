<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderDetailsResource extends JsonResource
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
            'name' => $this->first_name . ' ' . $this->last_name,
            'service_category_id' => $this->serviceCategory?->id,
            'service_category_name' => $this->serviceCategory?->name,
            'experience_years' => $this->experience_years,
            'description' => $this->description,
            'image_url' => $this->image?->image_url,
            'rating' => $this->rating,
            'rating_count' => $this->rating_count,
            'is_available' => $this->is_available,
            'address' => $this->address,
            'services' => $this->services,
            'reviews' => $this->reviews()->with('user')->get(),
            'created_at' => $this->created_at,
        ];
    }
}
