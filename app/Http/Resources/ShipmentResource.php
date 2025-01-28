<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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
            'shippers' => ShipperResource::collection($this->whenLoaded('shippers')),
            'carrier' => new CarrierResource($this->whenLoaded('carrier')),
            'stops' => ShipmentStopResource::collection($this->whenLoaded('stops')),
        ];
    }
}
