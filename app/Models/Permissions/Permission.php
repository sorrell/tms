<?php

namespace App\Models\Permissions;

use App\Enums\Permission as EnumsPermission;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{

    protected $appends = ['label'];

    // create a label attribute
    public function getLabelAttribute()
    {
        return EnumsPermission::tryFrom($this->name)?->label();
    }
}
