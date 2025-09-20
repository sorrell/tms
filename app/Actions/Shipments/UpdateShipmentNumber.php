<?php

namespace App\Actions\Shipments;

use App\Enums\TemperatureUnit;
use App\Http\Requests\Shipments\UpdateShipmentNumberRequest;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Testing\Fakes\EventFake;

class UpdateShipmentNumber
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        ?string $shipmentNumber = null,
    ): Shipment
    {
        $shipment->update([
            'shipment_number' => $shipmentNumber,
        ]);

        if (Event::getFacadeRoot() instanceof EventFake) {
            Shipment::dispatchUpdatedEventForModel($shipment);
        }


        return $shipment;
    }

    public function asController(UpdateShipmentNumberRequest $request, Shipment $shipment)
    {
        $shipment = $this->handle($shipment, $request->shipment_number);

        return redirect()->back()->with('success', 'Shipment number updated successfully');
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
