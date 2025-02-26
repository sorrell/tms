<?php

namespace App\Policies\Organizations;

use App\Enums\Permission;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationUser;
use App\Models\User;

class OrganizationUserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Organization $organization): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganizationUser $organizationUser): bool
    {
        return $organizationUser->organization->users->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $user->can(Permission::ORGANIZATION_MANAGE_USERS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrganizationUser $organizationUser): bool
    {
        return $user->can(Permission::ORGANIZATION_MANAGE_USERS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrganizationUser $organizationUser): bool
    {
        if ($organizationUser->user_id === $organizationUser->organization->owner_id) {
            return false;
        }
        return $user->can(Permission::ORGANIZATION_MANAGE_USERS);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrganizationUser $organizationUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrganizationUser $organizationUser): bool
    {
        return false;
    }
}
