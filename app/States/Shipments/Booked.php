<?php

namespace App\States\Shipments;

class Booked extends ShipmentState
{
    public static $name = 'booked';

    public function label(): string
    {
        return 'Booked';
    }
}
