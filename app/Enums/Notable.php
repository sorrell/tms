<?php
declare(strict_types=1);
namespace App\Enums;

use App\Models\Shipments\Shipment;
use Illuminate\Database\Eloquent\Model;

enum Notable: string
{
    case SHIPMENT = 'shipment';


    public function getClassName(): string
    {
        return match ($this) {
            self::SHIPMENT => Shipment::class,
        };
    }
}