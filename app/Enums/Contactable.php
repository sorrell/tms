<?php
declare(strict_types=1);
namespace App\Enums;

use App\Models\Carriers\Carrier;
use App\Models\Customers\Customer;
use App\Models\Facility;
use App\Models\Shipments\Shipment;

enum Contactable: string
{
    case SHIPMENT = 'shipment';
    case CUSTOMER = 'customer';
    case CARRIER = 'carrier';
    case FACILITY = 'facility';
    
    public function getClassName(): string
    {
        return match ($this) {
            self::SHIPMENT => Shipment::class,
            self::CUSTOMER => Customer::class,
            self::CARRIER => Carrier::class,
            self::FACILITY => Facility::class,
        };
    }

    public function getContactTypes(): array {
        return match ($this) {
            self::SHIPMENT => [ContactType::GENERAL],
            self::CUSTOMER => [ContactType::GENERAL],
            self::CARRIER => [
                ContactType::GENERAL,
                ContactType::DRIVER,
                ContactType::DISPATCHER,
            ],
            self::FACILITY => [
                ContactType::GENERAL
            ],
        };
    }
}