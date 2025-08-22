<?php

namespace App\Events\Carriers;

use App\Events\Core\TmsEvent;
use App\Models\Carriers\Carrier;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class CarrierCreated extends TmsEvent implements ShouldBroadcast
{
    public function __construct(
        public readonly Carrier $carrier,
        array $metadata = []
    ) {
        parent::__construct(
            eventId: Str::uuid()->toString(),
            organizationId: $carrier->organization_id,
            occurredAt: now(),
            triggeredBy: auth()->user(),
            metadata: $metadata
        );
    }

    public function getEventType(): string
    {
        return 'carrier.created';
    }

    public function getEventData(): array
    {
        return [
            'entity_type' => Carrier::class,
            'entity_id' => $this->carrier->id,
            'carrier_id' => $this->carrier->id,
            'carrier_name' => $this->carrier->name,
            'mc_number' => $this->carrier->mc_number,
            'dot_number' => $this->carrier->dot_number,
            'status' => $this->carrier->status,
            'created_at' => $this->carrier->created_at->toDateTimeString(),
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}