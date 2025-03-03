<?php

namespace App\Actions\Shipments;

use App\Events\Shipments\ShipmentCarrierUpdated;
use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShipmentCarrierDetails
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        int $carrierId,
    ): Shipment {

        $shipment->update([
            'carrier_id' => $carrierId,
        ]);

        event(new ShipmentCarrierUpdated($shipment));

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->carrier_id,
        );

        return redirect()->back()->with('success', 'Shipment carrier details updated successfully');
    }

    public function rules(): array
    {
        return [
            'carrier_id' => ['required', 'exists:carriers,id'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
