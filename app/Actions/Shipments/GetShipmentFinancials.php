<?php


namespace App\Actions\Shipments;

use App\Http\Resources\ShipmentFinancialsResource;
use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShipmentFinancials {
    use AsAction;

    public function handle(Shipment $shipment) : Shipment
    {
        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        return $this->handle($shipment);
    }

    public function jsonResponse(Shipment $shipment)
    {
        return ShipmentFinancialsResource::make($shipment->load('shipment_customer_rates', 'shipment_carrier_rates', 'accessorials'));
    }

    public function htmlResponse(Shipment $shipment)
    {
        return response('No view available', 404);
    }
}