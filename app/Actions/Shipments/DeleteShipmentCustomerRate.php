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

class DeleteShipmentCustomerRate {
    use AsAction;

    public function handle(
        ShipmentCustomerRate $shipmentCustomerRate,
        
    ) : ShipmentCustomerRate
    {
        $shipmentCustomerRate->delete();
        return $shipmentCustomerRate;
    }

    public function asController(ActionRequest $request, ShipmentCustomerRate $shipmentCustomerRate)
    {
        return $this->handle(
            shipmentCustomerRate: $shipmentCustomerRate
        );
    }

    public function jsonResponse(ShipmentCustomerRate $shipmentCustomerRate)
    {
        return ShipmentCustomerRateResource::make($shipmentCustomerRate);
    }

    public function rules() {
        return [];
    }
}