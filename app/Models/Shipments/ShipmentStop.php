<?php

namespace App\Models\Shipments;

use App\Enums\StopType;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShipmentStop extends Model
{
    use HasFactory, HasOrganization;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'facility_id',
        'stop_type',
        'special_instructions',
        'reference_numbers',
        'stop_number',
        'eta',
        'arrived_at',
        'loaded_unloaded_at',
        'left_at',
        'appointment_at',
        'appointment_end_at',
        'appointment_type',
    ];

    protected $casts = [
        'stop_type' => StopType::class,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Default order to the stop number order
        static::addGlobalScope('order', function ($query) {
            $query->orderBy('stop_number', 'asc');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

}
