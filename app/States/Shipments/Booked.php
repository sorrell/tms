<?php

namespace App\States\Shipments;

class Booked extends ShipmentState
{
    public function label(): string
    {
        return 'Booked';
    }
}
