<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentStopAppointment extends Model
{
    use HasOrganization, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'shipment_stop_id',
        'appointment_at',
        'appointment_end_at',
        'appointment_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ShipmentStop, $this>
     */
    public function shipmentStop(): BelongsTo
    {
        return $this->belongsTo(ShipmentStop::class);
    }
}
