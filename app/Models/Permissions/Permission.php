<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $table = 'permissions';

    public function roles(): BelongsToMany
    {
        return $this->parent->roles()
            ->withPivotValue('organization_id', current_organization_id());
    }

    public function users() : BelongsToMany
    {
        return $this->parent->users()
            ->withPivotValue('organization_id', current_organization_id());
    }
}
