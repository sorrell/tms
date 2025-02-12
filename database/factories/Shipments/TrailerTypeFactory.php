<?php

namespace Database\Factories\Shipments;

use App\Models\Shipments\TrailerType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipments\TrailerType>
 */
class TrailerTypeFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Dry Van',
                'Reefer',
                'Flatbed',
                'Step Deck',
                'Double Drop',
                'Lowboy',
                'RGN',
                'Box Truck',
                'Power Only',
            ]),
        ];
    }
} 