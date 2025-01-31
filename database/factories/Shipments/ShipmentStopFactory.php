<?php

namespace Database\Factories\Shipments;

use App\Enums\StopType;
use App\Models\Location;
use App\Models\Organizations\Organization;
use App\Models\Carrier;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\TrailerType;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facility;
use App\Models\Shipments\ShipmentStop;
use Carbon\Carbon;


class ShipmentStopFactory extends Factory
{
    protected $model = ShipmentStop::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'facility_id' => Facility::factory(),
            'stop_type' => fake()->randomElement(StopType::cases()),
            'stop_number' => fake()->numberBetween(1, 5),
            'special_instructions' => fake()->optional()->sentence(),
            'reference_numbers' => fake()->optional()->words(3, true),
        ];
    }

    /**
     * Configure the model factory to create an appointment.
     *
     * @return $this
     */
    public function withAppointment(): static
    {
        return $this->afterCreating(function (ShipmentStop $stop) {
            $stop->appointment()->create([
                'appointment_at' => Carbon::now()->addDays(fake()->numberBetween(1, 14)),
            ]);
        });
    }
}