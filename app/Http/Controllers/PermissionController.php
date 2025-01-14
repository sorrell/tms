<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Models\Organizations\Organization;
use App\Models\Permissions\Role;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    public function assignRolePermission()
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        throw new \Nette\NotImplementedException();
    }

    public function removeRolePermission()
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        throw new \Nette\NotImplementedException();
    }

    public function assignUserRole()
    {
        Gate::authorize(Permission::ORGANIZATION_MANAGE_USERS);

        throw new \Nette\NotImplementedException();
    }

    public function removeUserRole()
    {
        Gate::authorize(Permission::ORGANIZATION_MANAGE_USERS);

        throw new \Nette\NotImplementedException();
    }

    public function storeRole()
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        throw new \Nette\NotImplementedException();
    }

    public function destroyRole(Organization $organization, Role $role)
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        $role->delete();

        return redirect()->back();

    }

    public function updateRole()
    {
        throw new \Nette\NotImplementedException();
    }



    // TODO - in the future with user manage page
    public function assignUserPermission()
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        throw new \Nette\NotImplementedException();
    }
    public function removeUserPermission()
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        throw new \Nette\NotImplementedException();
    }

}
