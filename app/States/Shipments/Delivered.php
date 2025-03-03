<?php

namespace App\States\Shipments;

class Delivered extends ShipmentState
{
    public static $name = 'delivered';

    public function label(): string
    {
        return 'Delivered';
    }
}
