<?php

namespace App\States\Shipments;

class Pending extends ShipmentState
{
    public function label(): string
    {
        return 'Pending';
    }
}