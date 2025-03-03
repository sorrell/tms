<?php

namespace App\States\Shipments;

class InTransit extends ShipmentState
{
    public function label(): string
    {
        return 'In Transit';
    }
}