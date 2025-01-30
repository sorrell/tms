<?php

namespace App\Actions\Shipments;

use App\Enums\TemperatureUnit;
use App\Http\Requests\Shipments\UpdateShipmentGeneralRequest;
use App\Http\Requests\Shipments\UpdateShipmentNumberRequest;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;

class UpdateShipmentGeneral
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        ?float $weight = null,
        ?float $tripDistance = null,
        ?int $trailerTypeId = null,
        ?float $trailerTemperature = null,
        ?float $trailerTemperatureMaximum = null,
        ?bool $trailerTemperatureRange = null,
    ): Shipment {
        $shipment->update([
            'weight' => $weight,
            'trip_distance' => $tripDistance,
            'trailer_type_id' => $trailerTypeId,
            'trailer_temperature' => $trailerTemperature,
            'trailer_temperature_maximum' => $trailerTemperatureMaximum,
            'trailer_temperature_range' => $trailerTemperatureRange,
        ]);

        return $shipment;
    }

    public function asController(UpdateShipmentGeneralRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->weight,
            $request->trip_distance,
            $request->trailer_type_id,
            $request->trailer_temperature,
            $request->trailer_temperature_maximum,
            $request->trailer_temperature_range,
        );

        return redirect()->back()->with('success', 'Shipment updated successfully');
    }
}
