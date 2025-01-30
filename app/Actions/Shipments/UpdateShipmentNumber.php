<?php

namespace App\Actions\Shipments;

use App\Enums\TemperatureUnit;
use App\Http\Requests\Shipments\UpdateShipmentNumberRequest;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;

class UpdateShipmentNumber
{
    use AsAction;

    public function handle(
        string $shipmentNumber = null,
        Shipment $shipment
    ): Shipment
    {
        $shipment->update([
            'shipment_number' => $shipmentNumber,
        ]);

        return $shipment;
    }

    public function asController(UpdateShipmentNumberRequest $request, Shipment $shipment)
    {
        $shipment = $this->handle($request->shipment_number, $shipment);

        return redirect()->back()->with('success', 'Shipment number updated successfully');
    }
}
