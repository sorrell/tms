<?php

namespace Database\Factories\Shipments;

use App\Models\Location;
use App\Models\Organizations\Organization;
use App\Models\Carrier;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\TrailerType;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facility;
use App\Models\Shipments\ShipmentStop;
use App\Models\Shipper;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipments\Shipment>
 */
class ShipmentFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'carrier_id' => Carrier::factory(),
            'weight' => fake()->randomFloat(2, 1000, 45000), // typical truck load weights in lbs
            'trip_distance' => fake()->randomFloat(2, 10, 3000), // distance in miles
            'trailer_type_id' => TrailerType::inRandomOrder()->first()->id,
            'trailer_temperature_range' => fake()->randomElement(['frozen', 'refrigerated', 'ambient']),
            'trailer_temperature' => fake()->randomFloat(1, -10, 70), // temperature in fahrenheit
            'trailer_temperature_maximum' => fake()->randomFloat(1, -10, 70),
            'shipment_number' => fake()->unique()->numerify('######'),
        ];
    }

    /**
     * Configure the model factory to attach shippers and create stops.
     *
     * @return $this
     */
    public function withShippersAndStops(int $numberOfStops = 2): static
    {
        return $this->afterCreating(function (Shipment $shipment) use ($numberOfStops) {
            // Attach shippers (typically 1-3)
            $shipment->shippers()->attach(
                Shipper::factory()->count(fake()->numberBetween(1, 3))->create()
            );

            // Create stops with appointments
            ShipmentStop::factory()
                ->count($numberOfStops)
                ->sequence(fn ($sequence) => ['stop_number' => $sequence->index + 1])
                ->for($shipment)
                ->withAppointment()
                ->create();
        });
    }
}



