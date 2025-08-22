<?php

namespace App\Listeners\Shipments;

use App\Events\Shipments\ShipmentStateChanged;
use App\Events\Shipments\ShipmentStateChangedTms;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RelayShipmentStateChanged implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ShipmentStateChanged $event): void
    {
        $shipment = $event->model;
        $previous = method_exists($event->initialState, 'value') ? $event->initialState->value() : (string) $event->initialState;
        $current = method_exists($event->finalState, 'value') ? $event->finalState->value() : (string) $event->finalState;

        event(new ShipmentStateChangedTms(
            shipment: $shipment,
            previousState: $previous,
            currentState: $current,
            metadata: [
                'updated_via' => 'model_state_change',
            ]
        ));
    }
} 