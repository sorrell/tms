<?php

namespace App\Models\Shipments;

use App\Enums\StopType;
use App\Events\Shipments\ShipmentUpdated;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasOrganization;
use App\Traits\HasAliases;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentStop extends Model
{
    use HasFactory, HasOrganization, HasAliases;

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

    public $aliasName = 'stop';
    public $aliasProperties = [
        'number' => 'stop_number',
        'type' => 'stop_type',
        
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

        // When a stop is updated, fire the ShipmentStopsUpdated event
        static::updated(function ($stop) {
            if ($stop->shipment) {
                event(new \App\Events\Shipments\ShipmentStopsUpdated($stop->shipment));

                // Also fire the TMS ShipmentUpdated event
                event(new ShipmentUpdated(
                    shipment: $stop->shipment->fresh(),
                    changedAttributes: ['stops_updated' => true],
                    previousAttributes: [],
                    metadata: [
                        'updated_via' => 'stop_model_update',
                        'stop_id' => $stop->id,
                        'stop_number' => $stop->stop_number,
                    ]
                ));
            }
        });

        // Also fire the event when a stop is created
        static::created(function ($stop) {
            if ($stop->shipment) {
                event(new \App\Events\Shipments\ShipmentStopsUpdated($stop->shipment));

                // Also fire the TMS ShipmentUpdated event
                event(new ShipmentUpdated(
                    shipment: $stop->shipment->fresh(),
                    changedAttributes: ['stop_added' => true],
                    previousAttributes: [],
                    metadata: [
                        'updated_via' => 'stop_model_create',
                        'stop_id' => $stop->id,
                        'stop_number' => $stop->stop_number,
                    ]
                ));
            }
        });

        // And when a stop is deleted
        static::deleted(function ($stop) {
            if ($stop->shipment) {
                event(new \App\Events\Shipments\ShipmentStopsUpdated($stop->shipment));

                // Also fire the TMS ShipmentUpdated event
                event(new ShipmentUpdated(
                    shipment: $stop->shipment->fresh(),
                    changedAttributes: ['stop_deleted' => true],
                    previousAttributes: [],
                    metadata: [
                        'updated_via' => 'stop_model_delete',
                        'stop_id' => $stop->id,
                        'stop_number' => $stop->stop_number,
                    ]
                ));
            }
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
