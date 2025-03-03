<?php

namespace App\Listeners\Shipments;

use App\Events\Shipments\ShipmentCarrierChanged;
use App\States\Shipments\Booked;
use App\States\Shipments\Pending;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateShipmentState
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     */
    public function handle(ShipmentCarrierChanged $event): void
    {
        switch(get_class($event)) {
            case ShipmentCarrierChanged::class:
                $this->handleCarrierChanged($event);
                break;
        }
    }

    protected function handleCarrierChanged(ShipmentCarrierChanged $event): void
    {
        if ($event->shipment->state::class === Pending::class) {
            $event->shipment->state->transitionTo(Booked::class);
        }
    }
}
