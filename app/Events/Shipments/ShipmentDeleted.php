<?php

namespace App\Events\Shipments;

use App\Events\Core\TmsEvent;
use App\Models\Shipments\Shipment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class ShipmentDeleted extends TmsEvent implements ShouldBroadcast
{
    public function __construct(
        public readonly Shipment $shipment,
        array $metadata = []
    ) {
        parent::__construct(
            eventId: Str::uuid()->toString(),
            organizationId: $shipment->organization_id,
            occurredAt: now(),
            triggeredBy: auth()->user(),
            metadata: $metadata
        );
    }

    public function getEventType(): string
    {
        return 'shipment.deleted';
    }

    public function getEventData(): array
    {
        return [
            'entity_type' => Shipment::class,
            'entity_id' => $this->shipment->id,
            'shipment_id' => $this->shipment->id,
            'shipment_number' => $this->shipment->shipment_number,
            'deleted_at' => now()->toDateTimeString(),
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}