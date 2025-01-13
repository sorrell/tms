<?php

namespace App\Traits;

use App\Models\Organizations\Organization;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(\App\Models\Scopes\OrganizationScope::class)]
trait HasOrganization
{
    public static function bootHasOrganization()
    {
        static::creating(function ($model) {
            $model->organization_id = $model->organization_id ?? current_organization_id();
        });

        static::updating(function ($model) {
            $model->organization_id = $model->organization_id ?? current_organization_id();
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
