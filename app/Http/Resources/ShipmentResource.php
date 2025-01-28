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
            'weight' => $this->weight,
            'trip_miles' => $this->trip_miles,
            'trailer_type' => new TrailerTypeResource($this->whenLoaded('trailer_type')),
            'trailer_temperature_range' => $this->trailer_temperature_range,
            'trailer_temperature_minimum' => $this->trailer_temperature_minimum,
            'trailer_temperature_maximum' => $this->trailer_temperature_maximum,
            'trailer_temperature_unit' => $this->trailer_temperature_unit,
            'shippers' => ShipperResource::collection($this->whenLoaded('shippers')),
            'carrier' => new CarrierResource($this->whenLoaded('carrier')),
            'stops' => ShipmentStopResource::collection($this->whenLoaded('stops')),
        ];
    }
}
