<?php

namespace App\States\Shipments;

class InTransit extends ShipmentState
{
    public static $name = 'in_transit';
    public function label(): string
    {
        return 'In Transit';
    }
}