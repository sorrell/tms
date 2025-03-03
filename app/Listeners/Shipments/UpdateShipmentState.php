<?php

namespace App\Listeners\Shipments;

use App\Enums\StopType;
use App\Events\Shipments\ShipmentCarrierUpdated;
use App\Events\Shipments\ShipmentStopsUpdated;
use App\Models\Shipments\Shipment;
use App\States\Shipments\AtDelivery;
use App\States\Shipments\AtPickup;
use App\States\Shipments\Booked;
use App\States\Shipments\Canceled;
use App\States\Shipments\Delivered;
use App\States\Shipments\Dispatched;
use App\States\Shipments\InTransit;
use App\States\Shipments\Pending;
use App\States\Shipments\ShipmentState;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateShipmentState
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ShipmentCarrierUpdated|ShipmentStopsUpdated $event): void
    {
        switch (get_class($event)) {
            case ShipmentCarrierUpdated::class:
                $this->handleCarrierChanged($event);
                break;
            case ShipmentStopsUpdated::class:
                $this->handleStopsChanged($event);
                break;
        }
    }

    protected function handleCarrierChanged(ShipmentCarrierUpdated $event): void
    {
        if ($event->shipment->state::class === Pending::class) {
            $event->shipment->state->transitionTo(Booked::class);
        }
    }

    protected function handleStopsChanged(ShipmentStopsUpdated $event): void
    {

        if (
            !in_array(
                $event->shipment->state::class,
                [
                    Dispatched::class,
                    InTransit::class,
                    AtPickup::class,
                    AtDelivery::class,
                    Delivered::class
                ]
            )
        ) {
            // If shipment is not in a state where stops are still active then
            // we can just return since we dont want to 
            // update the state for a pending or completed shipment
            return;
        }


        $shipment = $event->shipment;

        $finalState = $this->calculateStopState($shipment);

        $shipment = $this->transitionThroughStopStates($shipment, $finalState);
    }

    /**
     * Calculates the state of the shipment based on the current state of the stops
     * This assumes that the shipment is in a state where stops are still active
     */
    private function calculateStopState(Shipment $shipment) : string
    {
        
        if ($shipment->stops()->latest('stop_number')->first()->loaded_unloaded_at) {
            return Delivered::class;
        }

        $currentStop = $shipment->current_stop;

        if ($currentStop && $currentStop->stop_type === StopType::Pickup) {
            return AtPickup::class;
        } elseif ($currentStop && $currentStop->stop_type === StopType::Delivery) {
            return AtDelivery::class;
        }

        if ($shipment->next_stop?->id != $shipment->stops()->first()->id) {
            return InTransit::class;
        }

        return Dispatched::class;
    }

    /**
     * Transitions the shipment through the required stop states to achieve the final state
     * based on the correct order
     * This supports multiple stop updates at once while still emitting required events
     */
    private function transitionThroughStopStates(Shipment $shipment, string $finalState) : Shipment
    {

        // TODO - There will be a bug in the case of multi picks
        // The pickup state will only be emitted once in the case of all stops being
        // updated at the same time.

        // TODO - This should get pulled in from the ShipmentState class somehow
        // Order of states to be transitioned through
        $stateOrder = [
            Dispatched::class,
            AtPickup::class,
            InTransit::class,
            AtDelivery::class,
            Delivered::class
        ];

        $currentState = $shipment->state::class;

        $currentStateIndex = array_search($currentState, $stateOrder);
        $finalStateIndex = array_search($finalState, $stateOrder);

        // If state is going back to a previous state 
        // then we force transition and skip events by updating 
        // the shipment directly
        // else we transition through the states one by one
        // to reach the final state
        if ($currentStateIndex > $finalStateIndex) {
            $shipment->update([
                'state' => ShipmentState::resolveStateClass($stateOrder[$finalStateIndex])
            ]);
        } elseif ($currentStateIndex < $finalStateIndex) {
            while($currentStateIndex < $finalStateIndex) {
                $shipment->state->transitionTo($stateOrder[$currentStateIndex+1]);
                $currentStateIndex++;
            }
        }

        return $shipment;
    }
}
