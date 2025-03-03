<?php

namespace App\States\Shipments;

class AtPickup extends ShipmentState
{
    public static $name = 'at_pickup';

    public function label(): string
    {
        return 'At Pickup';
    }
}