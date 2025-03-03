<?php

namespace Database\Factories\Shipments;

use App\Models\Location;
use App\Models\Organizations\Organization;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\TrailerType;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facility;
use App\Models\Shipments\ShipmentStop;
use App\Models\Shipments\TrailerSize;
use App\Models\Customers\Customer;
use App\States\Shipments\Booked;
use App\States\Shipments\Pending;
use App\States\Shipments\ShipmentState;
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
            'weight' => fake()->randomFloat(2, 1000, 45000), // typical truck load weights in lbs
            'trip_distance' => fake()->randomFloat(2, 10, 3000), // distance in miles
            'trailer_type_id' => TrailerType::inRandomOrder()->first()->id,
            'trailer_size_id' => TrailerSize::inRandomOrder()->first()->id,
            'trailer_temperature_range' => fake()->boolean(),
            'trailer_temperature' => fake()->randomFloat(1, -10, 70), // temperature in fahrenheit
            'trailer_temperature_maximum' => fake()->randomFloat(1, -10, 70),
            'shipment_number' => fake()->unique()->numerify('######'),
            'state' => Pending::class,
        ];
    }

    public function withCarrier(): static
    {
        return $this->state([
            'carrier_id' => Carrier::factory(),
            'state' => Booked::class,
        ]);
    }

    /**
     * Configure the model factory to attach customers and create stops.
     *
     * @return $this
     */
    public function withCustomersAndStops(int $numberOfStops = 2): static
    {
        return $this->afterCreating(function (Shipment $shipment) use ($numberOfStops) {
            // Attach customers (typically 1-3)
            $shipment->customers()->attach(
                Customer::factory()->count(fake()->numberBetween(1, 3))->create()
            );

            // Create stops with appointments
            ShipmentStop::factory()
                ->count($numberOfStops)
                ->sequence(fn ($sequence) => ['stop_number' => $sequence->index + 1])
                ->for($shipment)
                ->create();
        });
    }
}



