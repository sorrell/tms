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
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'shipment_id' => $this->shipment_id,
            'carrier_id' => $this->carrier_id,
            'carrier' => $this->whenLoaded('carrier', function($carrier) {
                return [
                    'id' => $carrier->id,
                    'name' => $carrier->name,
                ];
            }),
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'carrier_rate_type_id' => $this->carrier_rate_type_id,
            'carrier_rate_type' => $this->whenLoaded('carrier_rate_type', function($carrierRateType) {
                return [
                    'id' => $carrierRateType->id,
                    'name' => $carrierRateType->name,
                ];
            }),
            'currency_id' => $this->currency_id,
            'currency' => $this->whenLoaded('currency', function($currency) {
                return [
                    'id' => $currency->id,
                    'code' => $currency->code,
                    'symbol' => $currency->symbol,
                ];
            }),
        ];
    }
}
