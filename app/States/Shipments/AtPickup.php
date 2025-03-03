<?php

namespace App\States\Shipments;

class AtPickup extends ShipmentState
{
    public function label(): string
    {
        return 'At Pickup';
    }
}