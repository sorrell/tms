<?php

namespace App\States\Shipments;

class Dispatched extends ShipmentState
{
    public static $name = 'dispatched';

    public function label(): string
    {
        return 'Dispatched';
    }
}