<?php

namespace App\States\Shipments;

class Canceled extends ShipmentState
{
    public function label(): string
    {
        return 'Canceled';
    }
}
