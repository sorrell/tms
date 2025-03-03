<?php

namespace App\States\Shipments;

class AtDelivery extends ShipmentState
{
    public function label(): string
    {
        return 'At Delivery';
    }
}
