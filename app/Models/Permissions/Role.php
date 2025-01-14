<?php

namespace App\Models\Permissions;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends \Spatie\Permission\Models\Role
{
    protected $table = 'roles';

    use HasOrganization;


    public function permissions() : BelongsToMany
    {
        return $this->parent->permissions()
            ->withPivotValue('organization_id', $this->organization_id);
    }

    public function users() : BelongsToMany
    {
        return $this->parent->users()
            ->withPivotValue('organization_id', $this->organization_id);
    }
}
