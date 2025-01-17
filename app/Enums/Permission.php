<?php
declare(strict_types=1);
namespace App\Enums;

use App\Models\User;

enum Permission: string
{
    // case NAMEINAPP = 'name-in-database';
    case ORGANIZATION_MANAGE_USERS = 'organization-manage-users';
    case ORGANIZATION_MANAGER = 'organization-manager';


    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            self::ORGANIZATION_MANAGE_USERS => 'Manage Organization Users & Permissions',
            self::ORGANIZATION_MANAGER => 'View & Edit Organization Details',
            default => 'Label Unknown',
        };
    }

    public static function syncToDatabase(): bool
    {
        foreach (static::cases() as $permission) {
            if (!\Spatie\Permission\Models\Permission::where('name', $permission->value)->exists()) {
                \Spatie\Permission\Models\Permission::create(['name' => $permission->value]);
            }
        }

        return true;
    }

    public static function getPermissionsForUser(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $permissions = [];
        foreach (static::cases() as $permission) {
            $permissions[$permission->name] = $user->can($permission) ? true : false;
        }
        return $permissions;
    }
}
