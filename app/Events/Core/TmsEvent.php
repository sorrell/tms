<?php

namespace App\Events\Core;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class TmsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToChannels;

    public function __construct(
        public readonly string $eventId,
        public readonly string $organizationId,
        public readonly \DateTimeInterface $occurredAt,
        public readonly ?User $triggeredBy = null,
        public readonly array $metadata = []
    ) {}

    abstract public function getEventType(): string;
    
    abstract public function getEventData(): array;
    
    abstract public function shouldBroadcast(): bool;
    
    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_type' => $this->getEventType(),
            'organization_id' => $this->organizationId,
            'occurred_at' => $this->occurredAt->format('c'),
            'triggered_by' => $this->triggeredBy?->id,
            'data' => $this->getEventData(),
            'metadata' => $this->metadata,
        ];
    }
}