<?php

namespace Database\Seeders;

use App\Models\Carriers\Carrier;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Shipments\Shipment;
use App\Models\Customers\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoEnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (current_organization_id() == null) {
            throw new \Exception('Demo environment seeder must be run with an authenticated organization');
        }

        Facility::factory(10)->create();
        Carrier::factory(10)->create();
        Customer::factory(10)->create();
        Location::factory(10)->create();
        Shipment::factory(10)->withCustomersAndStops(2)->create();
    }
}
