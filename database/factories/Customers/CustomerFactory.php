<?php

namespace Database\Factories\Customers;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $suffixes = ['Manufacturing', 'Industries', 'Products', 'Supply Co.', 'Production'];
        $prefixes = ['Global', 'United', 'Premier', 'Advanced', 'Industrial', 'Standard', 'Quality'];
        
        return [
            'name' => fake()->randomElement($prefixes) . ' ' . 
                     fake()->company() . ' ' . 
                     fake()->randomElement($suffixes),
        ];
    }
}

