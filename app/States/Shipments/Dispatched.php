<?php

namespace App\States\Shipments;

class Dispatched extends ShipmentState
{
    public function label(): string
    {
        return 'Dispatched';
    }
}