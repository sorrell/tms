<?php

namespace Database\Seeders;

use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationUser;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoOrgSeeder extends Seeder
{
    /**
     * Seed a whole new fake org and user with a demo env
     */
    public function run(): void
    {

        $adminUser = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $regularUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
        ]);

        $org = Organization::create([
            'name' => 'Test Organization',
            'owner_id' => $adminUser->id,
        ]);

        OrganizationUser::create([
            'organization_id' => $org->id,
            'user_id' => $adminUser->id,
        ]);

        OrganizationUser::create([
            'organization_id' => $org->id,
            'user_id' => $regularUser->id,
        ]);

        $adminUser->update([
            'current_organization_id' => $org->id,
        ]);

        $regularUser->update([
            'current_organization_id' => $org->id,
        ]);

        run_with_organization($org->id, function () {
            $this->call(DemoEnvironmentSeeder::class);
        });
    }

}
