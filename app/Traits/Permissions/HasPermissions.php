<?php

use Spatie\Permission\Traits\HasPermissions as TraitsHasPermissions;

trait HasPermissions
{
    use TraitsHasPermissions {
        permissions as private parentPermissions;
    }

    public function permissions()
    {
        return $this->parentPermissions()->withPivotValue('organization_id', $this->organization_id);
    }

}
