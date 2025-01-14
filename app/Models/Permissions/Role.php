<?php

namespace App\Models\Permissions;

use App\Traits\HasOrganization;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    use HasOrganization;
}
