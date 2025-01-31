<?php

namespace App\Actions\Shipments;

use App\Enums\TemperatureUnit;
use App\Http\Requests\Shipments\UpdateShipmentGeneralRequest;
use App\Http\Requests\Shipments\UpdateShipmentNumberRequest;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;

class UpdateShipmentStops
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        array $stops,
    ): Shipment {

        foreach ($stops as $stop) {
            //$shipment->stops()->updateOrCreate(['id' => $stop['id']], $stop);
        }

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->stops,
        );

        return redirect()->back()->with('success', 'Shipment stops updated');
    }

    public function rules(): array
    {
        return [
            'stops' => ['required', 'array'],
            'stops.*.id' => ['integer'],
            'stops.*.stop_number' => ['required', 'integer'],
            'stops.*.stop_type' => ['required', 'string'],
            'stops.*.facility_id' => ['required', 'integer'],
            'stops.*.special_instructions' => ['string'],
            'stops.*.reference_numbers' => ['string'],
            'stops.*.appointment_type' => ['required', 'string'],
            'stops.*.appointment_at' => ['required', 'date'],
            'stops.*.appointment_end_at' => ['date'],
            'stops.*.arrived_at' => ['date'],
            'stops.*.eta' => ['date'],
            'stops.*.loaded_unloaded_at' => ['date'],
            'stops.*.left_at' => ['date'],
        ];
    }
}
