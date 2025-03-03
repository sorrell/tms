<?php
declare(strict_types=1);
namespace App\Enums;

use App\Models\Carrier;
use App\Models\Customers\Customer;
use App\Models\Shipments\Shipment;

enum Contactable: string
{
    case SHIPMENT = 'shipment';
    case CUSTOMER = 'customer';
    case CARRIER = 'carrier';
    public function getClassName(): string
    {
        return match ($this) {
            self::SHIPMENT => Shipment::class,
            self::CUSTOMER => Customer::class,
            self::CARRIER => Carrier::class,
        };
    }
}