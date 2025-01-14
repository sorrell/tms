<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRole;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Organizations\Organization;
use App\Models\Permissions\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{

    public function updateRole(UpdateRoleRequest $request, Organization $organization, Role $role)
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        $role->syncPermissions($request->permissions);

        // Handle users by removing anyone not in the new list
        // then adding anyone in the new list  that isn't already in the role
        $removeUsers = $role->users()->whereNotIn('id', $request->users)->get();
        $role->users()->detach($removeUsers);
        $addUsers = User::whereIn('id', $request->users)->get();
        foreach($addUsers as $addUser) {
            $addUser->assignRole($role);
        }

        $role->name = $request->name;

        $role->save();

        return redirect()->back()->with('success', 'Role updated successfully');
    }

    public function storeRole(StoreRoleRequest $request, Organization $organization)
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        $role = Role::create([
            'name' => request('name'),
        ]);

        $role->syncPermissions($request->permissions);

        $users = User::whereIn('id', $request->users)->get();
        foreach($users as $user) {
            $user->assignRole($role);
        }

        return redirect()->back()->with('success', 'Role created successfully');
    }


    public function destroyRole(Organization $organization, Role $role)
    {
        Gate::authorize(Permission::ORGANIZATION_ADMIN);

        $role->delete();

        return redirect()->back();
    }
}
