<?php

namespace App\Providers;

class PermissionRegistrar extends \Spatie\Permission\PermissionRegistrar
{
    /**
     * Set the team id for teams/groups support, this id is used when querying permissions/roles
     *
     * @param  int|string|\Illuminate\Database\Eloquent\Model|null  $id
     */
    public function setPermissionsTeamId($id): void
    {
        auth()->user()->current_organization_id = $id;
        auth()->user()->save();
    }

    /**
     * @return int|string|null
     */
    public function getPermissionsTeamId()
    {
        return current_organization_id();
    }
}
