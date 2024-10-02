<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceProviderResource extends JsonResource
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
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'city' => $this->city,
            'service_type' => json_decode($this->service_type),  // Ensure proper decoding
            'years_of_experience' => $this->years_of_experience,
            'availability' => $this->availability,
            'description' => $this->description,
            'languages' => json_decode($this->languages),  // Ensure proper decoding
            'profile_image' => $this->profile_image ? $this->profile_image : null,  // Ensure correct handling of profile image
        ];
        
    }
}
