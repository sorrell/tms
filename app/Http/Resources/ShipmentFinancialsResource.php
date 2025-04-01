<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\Shipment
 */
class ShipmentFinancialsResource extends JsonResource
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
            'shipment_customer_rates' => ShipmentCustomerRateResource::collection($this->shipment_customer_rates->load('customer', 'customer_rate_type', 'currency')),
            'shipment_carrier_rates' => ShipmentCarrierRateResource::collection($this->shipment_carrier_rates->load('carrier', 'carrier_rate_type', 'currency')),
            'accessorials' => AccessorialResource::collection($this->accessorials),
        ];
    }
}
