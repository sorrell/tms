<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasOrganization;

class ShipmentCustomer extends Pivot
{
    use HasOrganization;

    protected $table = 'shipment_customers';

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'customer_id',
    ];
}
