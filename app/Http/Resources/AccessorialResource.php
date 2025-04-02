<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\Accessorial
 */
class AccessorialResource extends JsonResource
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
            'customer' => $this->whenLoaded('customer', function($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ];
            }),
            'carrier_id' => $this->carrier_id,
            'carrier' => $this->whenLoaded('carrier', function($carrier) {
                return [
                    'id' => $carrier->id,
                    'name' => $carrier->name,
                ];
            }),
            'invoice_customer' => $this->invoice_customer,
            'pay_carrier' => $this->pay_carrier,
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'accessorial_type_id' => $this->accessorial_type_id,
            'accessorial_type' => $this->whenLoaded('accessorial_type', function($accessorialType) {
                return [
                    'id' => $accessorialType->id,
                    'name' => $accessorialType->name,
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
