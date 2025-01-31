<?php

namespace App\Actions\Shipments;

use App\Enums\TemperatureUnit;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;

class CreateShipment
{
    use AsAction;

    public function handle(
        array $shipperIds,
        int $carrierId,
        array $stops,
        ?float $weight = null,
        ?float $tripDistance = null,
        ?int $trailerTypeId = null,
        ?bool $trailerTemperatureRange = false,
        ?float $trailerTemperature = null,
        ?float $trailerTemperatureMaximum = null,
        ?string $shipmentNumber = null,
    ): Shipment
    {
        DB::beginTransaction();

        $shipment = Shipment::create([
            'carrier_id' => $carrierId,
            'weight' => $weight,
            'trip_distance' => $tripDistance,
            'trailer_type_id' => $trailerTypeId,
            'trailer_temperature_range' => $trailerTemperatureRange,
            'trailer_temperature' => $trailerTemperature,
            'trailer_temperature_maximum' => $trailerTemperatureMaximum,
            'shipment_number' => $shipmentNumber,
        ]);

        $shipment->shippers()->attach($shipperIds);

        foreach ($stops as $stopData) {
            $stop = $shipment->stops()->create([
                'facility_id' => $stopData['facility_id'],
                'stop_type' => $stopData['stop_type'],
                'stop_number' => $stopData['stop_number'],
                'special_instructions' => $stopData['special_instructions'],
                'reference_numbers' => $stopData['reference_numbers'],
                'appointment_at' => Carbon::parse($stopData['appointment_at']),
            ]);
        }

        DB::commit();

        return $shipment;
    }
}
