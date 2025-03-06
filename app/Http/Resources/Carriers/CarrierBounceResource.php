<?php

namespace App\Http\Resources\Carriers;

use App\Http\Resources\ShipmentResource;
use App\Http\Resources\UserResource;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Carriers\CarrierBounce
 */
class CarrierBounceResource extends JsonResource
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
            'shipment_id' => $this->shipment_id,
            'carrier_id' => $this->carrier_id,
            'bounce_type' => $this->bounce_type,
            'bounced_by' => $this->bounced_by,
            'reason' => $this->reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'bounced_by_user' => $this->whenLoaded('bouncedBy', fn (User $user) => new UserResource($user)),
            'shipment' => $this->whenLoaded('shipment', fn (Shipment $shipment) => new ShipmentResource($shipment)),
            'carrier' => $this->whenLoaded('carrier', fn (Carrier $carrier) => new CarrierResource($carrier)),
        ];
    }
}
