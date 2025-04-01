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
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'shipment_id' => $this->shipment_id,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', function($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ];
            }),
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'customer_rate_type_id' => $this->customer_rate_type_id,
            'customer_rate_type' => $this->whenLoaded('customer_rate_type', function($customerRateType) {
                return [
                    'id' => $customerRateType->id,
                    'name' => $customerRateType->name,
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
