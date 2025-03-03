<?php

namespace App\States\Shipments;

class Canceled extends ShipmentState
{
    public static $name = 'canceled';

    public function label(): string
    {
        return 'Canceled';
    }
}
