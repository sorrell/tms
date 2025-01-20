<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;

class CreateShipment
{
    use AsAction;

    public function handle(): Shipment
    {
        throw new NotImplementedException();
    }
}
