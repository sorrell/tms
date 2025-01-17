<?php

namespace App\Traits;

use App\Models\Organizations\Organization;
use Illuminate\Database\Eloquent\Builder;

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

        static::addGlobalScope(new \App\Models\Scopes\OrganizationScope);
    }

    public static function allOrganizations() : Builder
    {
        return static::withoutGlobalScope(\App\Models\Scopes\OrganizationScope::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
