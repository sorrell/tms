<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasOrganization;

class ShipmentStop extends Model
{
    use HasFactory, HasOrganization;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'facility_id',
        'stop_type',
    ];
}
