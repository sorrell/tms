<?php

namespace App\Traits;

use App\Events\Carriers\CarrierAssigned;
use App\Events\Carriers\CarrierCreated;
use App\Events\Carriers\CarrierUnassigned;
use App\Events\Shipments\ShipmentCreated;
use App\Events\Shipments\ShipmentDeleted;
use App\Events\Shipments\ShipmentRestored;
use App\Events\Shipments\ShipmentUpdated;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;

trait DispatchesEvents
{
    public static function bootDispatchesEvents(): void
    {
        static::created(function ($model) {
            static::handleCreatedEvent($model);
        });

        static::updated(function ($model) {
            static::handleUpdatedEvent($model);
        });

        static::deleted(function ($model) {
            static::handleDeletedEvent($model);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                static::handleRestoredEvent($model);
            });
        }
    }

    protected static function handleCreatedEvent($model): void
    {
        switch (get_class($model)) {
            case Shipment::class:
                event(new ShipmentCreated($model, [
                    'created_via' => 'model_observer',
                    'source' => request()->route()?->getName() ?? 'unknown',
                ]));
                break;

            case Carrier::class:
                event(new CarrierCreated($model, [
                    'created_via' => 'model_observer',
                    'source' => request()->route()?->getName() ?? 'unknown',
                ]));
                break;
        }
    }

    protected static function handleUpdatedEvent($model): void
    {
        switch (get_class($model)) {
            case Shipment::class:
                static::handleShipmentUpdated($model);
                break;

            case Carrier::class:
                // Handle carrier updates if needed
                break;
        }
    }

    protected static function handleDeletedEvent($model): void
    {
        switch (get_class($model)) {
            case Shipment::class:
                event(new ShipmentDeleted($model, [
                    'deleted_via' => 'model_observer',
                    'soft_delete' => method_exists($model, 'trashed') && $model->trashed(),
                ]));
                break;
        }
    }

    protected static function handleRestoredEvent($model): void
    {
        switch (get_class($model)) {
            case Shipment::class:
                event(new ShipmentRestored($model, [
                    'restored_via' => 'model_observer',
                ]));
                break;
        }
    }

    protected static function handleShipmentUpdated(Shipment $shipment): void
    {
        $changes = $shipment->getChanges();
        
        // Check for carrier assignment/unassignment
        if (isset($changes['carrier_id'])) {
            $previousCarrierId = $shipment->getOriginal('carrier_id');
            $newCarrierId = $changes['carrier_id'];

            // Carrier was unassigned
            if ($previousCarrierId && !$newCarrierId) {
                $previousCarrier = Carrier::find($previousCarrierId);
                if ($previousCarrier) {
                    event(new CarrierUnassigned($shipment, $previousCarrier, [
                        'unassigned_via' => 'shipment_update',
                    ]));
                }
            }
            // Carrier was assigned
            elseif ($newCarrierId) {
                $newCarrier = Carrier::find($newCarrierId);
                $previousCarrier = $previousCarrierId ? Carrier::find($previousCarrierId) : null;
                
                if ($newCarrier) {
                    event(new CarrierAssigned($shipment, $newCarrier, $previousCarrier, [
                        'assigned_via' => 'shipment_update',
                        'is_reassignment' => !is_null($previousCarrierId),
                    ]));
                }
            }
        }

        // Always fire general shipment updated event
        event(new ShipmentUpdated(
            $shipment,
            $changes,
            [
                'updated_via' => 'model_observer',
                'has_carrier_change' => isset($changes['carrier_id']),
            ]
        ));
    }
}