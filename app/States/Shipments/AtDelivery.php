<?php

namespace App\States\Shipments;

class AtDelivery extends ShipmentState
{

    public static $name = 'at_delivery';

    public function label(): string
    {
        return 'At Delivery';
    }
}
