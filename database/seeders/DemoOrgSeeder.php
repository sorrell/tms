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
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);

        $org = Organization::create([
            'name' => 'Test Organization',
            'owner_id' => $user->id,
        ]);

        $orgMembership = OrganizationUser::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
        ]);

        $user->update([
            'current_organization_id' => $org->id,
        ]);

        run_with_organization($org->id, function () {
            $this->call(DemoEnvironmentSeeder::class);
        });
    }

}
