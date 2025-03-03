<?php

namespace App\States\Shipments;

class Pending extends ShipmentState
{
    public static $name = 'pending';
    public function label(): string
    {
        return 'Pending';
    }
}