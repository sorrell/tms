<?php

namespace Database\Factories\Organizations;

use App\Models\Organizations\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizations\Organization>
 */
class OrganizationFactory extends Factory
{
    
    public function definition()
    {
        return [
            'name' => fake()->company,
            'owner_id' => 1,
        ];
    }
} 