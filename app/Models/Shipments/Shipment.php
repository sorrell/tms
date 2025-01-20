<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasOrganization;

class Shipment extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'carrier_id',
    ];  
}
