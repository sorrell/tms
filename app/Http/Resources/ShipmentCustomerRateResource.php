<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\ShipmentCustomerRate
 */
class ShipmentCustomerRateResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'customer_rate_type_id' => $this->customer_rate_type_id,
            'currency_id' => $this->currency_id,
        ];
    }
}
