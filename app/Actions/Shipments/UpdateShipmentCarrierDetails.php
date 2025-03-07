<?php

namespace App\Actions\Shipments;

use App\Events\Shipments\ShipmentCarrierUpdated;
use App\Models\Carriers\Carrier;
use App\Models\Contact;
use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShipmentCarrierDetails
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        int $carrierId,
        ?int $driverId,
    ): Shipment {

        if($driverId && Carrier::find($carrierId)->contacts()->where('id', $driverId)->count() === 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'driver_id' => ['Selected driver is not associated with this carrier'],
            ]);
        }

        $shipment->carrier_id = $carrierId;
        $shipment->driver_id = $driverId;
        $shipment->save();

        event(new ShipmentCarrierUpdated($shipment));

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->carrier_id,
            $request->driver_id,
        );

        return redirect()->back()->with('success', 'Shipment carrier details updated successfully');
    }

    public function rules(): array
    {
        return [
            'carrier_id' => ['required', 'exists:carriers,id'],
            'driver_id' => ['nullable', 'exists:contacts,id'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
