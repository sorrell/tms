<?php

use Spatie\Permission\Traits\HasRoles as TraitsHasRoles;

trait HasRoles
{
    use TraitsHasRoles {
        roles as private parentRoles;
    }

    public function roles()
    {
        return $this->parentRoles()->withPivotValue('organization_id', current_organization_id());
    }

}
