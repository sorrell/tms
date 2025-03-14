<?php
declare(strict_types=1);
namespace App\Enums\Documents;

use App\Models\Carriers\Carrier;
use App\Models\Customers\Customer;
use App\Models\Facility;
use App\Models\Shipments\Shipment;

enum Documentable: string
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

    public static function fromClassName(string $className): static
    {
        return match($className) {
            Shipment::class => self::SHIPMENT,
            Customer::class => self::CUSTOMER,
            Carrier::class => self::CARRIER,
            Facility::class => self::FACILITY,
        };
    }

    public static function getDefaultFoldersByClassName(string $className) : array
    {
        return static::fromClassName($className)->getDefaultFolders();
    }

    public function getDefaultFolders() : array
    {
        return match($this) {
            self::SHIPMENT => [
                DocumentFolder::RATECONS,
                DocumentFolder::BOLS,
                DocumentFolder::CUSTOMER_INVOICE,
                DocumentFolder::CARRIER_BILLS,
            ],
            self::CUSTOMER => [],
            self::CARRIER => [],
            self::FACILITY => [],
        };
    }
}