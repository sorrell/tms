<?php

namespace App\Console\Commands;

use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Customer;
use Database\Seeders\DemoEnvironmentSeeder;
use Illuminate\Console\Command;

class DemoEnvironmentSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:setup {organization_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uses faker to create data to support a demo environment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $organizationId = intval($this->argument('organization_id'));

        run_with_organization($organizationId, function () {
            $this->call(DemoEnvironmentSeeder::class);
        });

        $this->info('Demo environment setup complete');
    }
}
