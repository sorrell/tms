<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Contact extends Model
{
    use HasOrganization, Searchable;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'mobile_phone',
        'office_phone',
        'office_phone_extension',
    ];

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return $this->name;
    }
}
