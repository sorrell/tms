<?php

namespace App\Http\Resources;

use App\Http\Resources\Carriers\CarrierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\Shipment
 */
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
            'shipment_number' => $this->shipment_number,
            'weight' => $this->weight,
            'trip_distance' => $this->trip_distance,
            'trailer_type' => new TrailerTypeResource($this->whenLoaded('trailer_type')),
            'trailer_size' => new TrailerSizeResource($this->whenLoaded('trailer_size')),
            'trailer_temperature_range' => $this->trailer_temperature_range,
            'trailer_temperature' => $this->trailer_temperature,
            'trailer_temperature_maximum' => $this->trailer_temperature_maximum,
            'driver_id' => $this->driver_id,
            'carrier_id' => $this->carrier_id,
            'customers' => CustomerResource::collection($this->whenLoaded('customers')),
            'carrier' => new CarrierResource($this->whenLoaded('carrier')),
            'driver' => new ContactResource($this->whenLoaded('driver')),
            'stops' => ShipmentStopResource::collection($this->whenLoaded('stops')),
            'lane' => $this->whenLoaded('stops', $this->lane()),
            'next_stop' => $this->whenLoaded('stops', new ShipmentStopResource($this->nextStop)),
            'state_label' => $this->state->label(),
            'state' => $this->state,
        ];
    }
}
