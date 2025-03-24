<?php


namespace App\Actions\Shipments;

use App\Http\Resources\ShipmentCustomerRateResource;
use App\Http\Resources\ShipmentFinancialsResource;
use App\Models\Accounting\Currency;
use App\Models\Accounting\CustomerRateType;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentCustomerRate;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateShipmentCustomerRate {
    use AsAction;

    public function handle(
        Shipment $shipment,
        int $customerId,
        float $rate,
        float $quantity,
        CustomerRateType $rateType
    ) : ShipmentCustomerRate
    {
        return $shipment->shipment_customer_rates()->create([
            'customer_id' => $customerId,
            'rate' => $rate,
            'quantity' => $quantity,
            'total' => $rate * $quantity,
            'customer_rate_type_id' => $rateType->id,
            'currency_id' => Currency::first()->id
        ]);
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        return $this->handle(
            shipment: $shipment,
            customerId: $request->customer_id,
            rate: $request->rate,
            quantity: $request->quantity,
            rateType: $request->rate_type,
        );
    }

    public function jsonResponse(ShipmentCustomerRate $shipmentCustomerRate)
    {
        return ShipmentCustomerRateResource::make($shipmentCustomerRate);
    }

    public function rules() {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'rate' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'rate_type' => ['required', 'exists:customer_rate_types,id'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}