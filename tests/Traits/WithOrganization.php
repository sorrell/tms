<?php

namespace Tests\Traits;

use App\Models\Organizations\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Context;

trait WithOrganization
{
    protected User $user;
    protected Organization $organization;

    public function setUpOrganization(): void
    {
        $this->user = User::factory()->create();
        $this->organization = Organization::create([
            'name' => 'Test Organization',
            'owner_id' => $this->user->id,
        ]);
        
        // Attach user to organization and set as current
        $this->user->organizations()->attach($this->organization);
        $this->user->current_organization_id = $this->organization->id;
        $this->user->save();

        Context::addHidden('current_organization_id', $this->organization->id);
    }
} 