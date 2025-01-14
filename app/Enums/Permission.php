<?php
declare(strict_types=1);
namespace App\Enums;

enum Permission: string
{
    // case NAMEINAPP = 'name-in-database';
    case ORGANIZATION_MANAGE_USERS = 'organization-manage-users';
    case ORGANIZATION_MANAGE_PERMISSIONS = 'organization-manage-permissions';


    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
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
}
