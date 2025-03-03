<?php

namespace App\States\Shipments;

class Delivered extends ShipmentState
{
    public function label(): string
    {
        return 'Delivered';
    }
}
