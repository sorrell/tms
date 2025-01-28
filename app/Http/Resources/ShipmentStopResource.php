<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Shipments\ShipmentStop
 */
class ShipmentStopResource extends JsonResource
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
            'facility' => new FacilityResource($this->whenLoaded('facility')),
            'stop_type' => $this->stop_type,
            'stop_number' => $this->stop_number,
            'special_instructions' => $this->special_instructions,
            'reference_numbers' => $this->reference_numbers,
        ];
    }
}
