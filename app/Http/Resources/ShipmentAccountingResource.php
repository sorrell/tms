<?php

namespace App\Http\Resources;

use App\Http\Resources\Accounting\PayableResource;
use App\Http\Resources\Accounting\ReceivableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\Shipment
 */
class ShipmentAccountingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payables' => PayableResource::collection($this->payables->load('rate_type', 'currency')),
            'receivables' => ReceivableResource::collection($this->receivables->load('rate_type', 'currency')),
        ];
    }
}
