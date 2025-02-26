<?php

namespace App\Policies\Organizations;

use App\Enums\Permission;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationInvitePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganizationInvite $organizationInvite): bool
    {
        return strtolower($user->email) === strtolower($organizationInvite->email);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id || $user->can(Permission::ORGANIZATION_MANAGE_USERS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrganizationInvite $organizationInvite): bool
    {
        return strtolower($user->email) === strtolower($organizationInvite->email)
            || $user->can(Permission::ORGANIZATION_MANAGE_USERS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrganizationInvite $organizationInvite): bool
    {
        return $user->can(Permission::ORGANIZATION_MANAGE_USERS);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrganizationInvite $organizationInvite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrganizationInvite $organizationInvite): bool
    {
        return false;
    }
}
