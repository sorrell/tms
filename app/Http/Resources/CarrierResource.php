<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Carrier
 */
class CarrierResource extends JsonResource
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
            'name' => $this->name,
            'mc_number' => $this->mc_number,
            'dot_number' => $this->dot_number,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'physical_location_id' => $this->physical_location_id,
            'billing_email' => $this->billing_email,
            'billing_phone' => $this->billing_phone,
            'billing_location_id' => $this->billing_location_id,

            'physical_location' => $this->whenLoaded('physical_location', function () {
                return new LocationResource($this->physical_location);
            }),
            
            'billing_location' => $this->whenLoaded('billing_location', function () {
                return new LocationResource($this->billing_location);
            }),
        ];
    }
}
