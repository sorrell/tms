<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Carrier extends Model
{
    use HasFactory, HasOrganization, Searchable;

    protected $fillable = [
        'organization_id',
        'name',
    ];

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s", $this->name);
    }


}
