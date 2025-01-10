<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(\App\Models\Scopes\OrganizationScope::class)]
trait HasOrganization
{
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
