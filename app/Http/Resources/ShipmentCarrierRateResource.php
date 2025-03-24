<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\ShipmentCarrierRate
 */
class ShipmentCarrierRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'organization_id' => $this->organization_id,
            'shipment_id' => $this->shipment_id,
            'carrier_id' => $this->carrier_id,
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'carrier_rate_type_id' => $this->carrier_rate_type_id,
            'currency_id' => $this->currency_id,
        ];
    }
}
