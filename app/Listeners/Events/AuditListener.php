<?php

namespace App\Listeners\Events;

use App\Contracts\Events\TmsEventContract;
use App\Support\Events\TmsEventRegistry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class AuditListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly TmsEventRegistry $registry)
    {
    }

    public function handle(TmsEventContract $event): void
    {
        try {
            // Only track events that are in our tracked list
            if (!$this->shouldTrackEvent($event)) {
                return;
            }

            // Store event as custom audit entry in OwenIT auditing system
            $audit = $this->createCustomAudit($event);

            // Log the event
            $triggeredBy = $event->getTriggeredBy();

            Log::channel('audit')->info('Event tracked in audit system', [
                'audit_id' => $audit->id,
                'event_id' => $event->getEventId(),
                'event_type' => $event->getEventType(),
                'organization_id' => $event->getOrganizationId(),
                'triggered_by' => $triggeredBy?->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to audit event', [
                'event_id' => $event->getEventId(),
                'event_type' => $event->getEventType(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function shouldTrackEvent(TmsEventContract $event): bool
    {
        return in_array($event->getEventType(), $this->registry->auditableEvents(), true);
    }

    protected function createCustomAudit(TmsEventContract $event): Audit
    {
        $eventData = $event->getEventData();
        $entityClass = $eventData['entity_type'] ?? null;
        $entityId = $eventData['entity_id'] ?? null;

        // Prepare old/new values based on event type
        $oldValues = $eventData['previous_attributes'] ?? [];
        $changedAttributes = $eventData['changed_attributes'] ?? [];

        $newValues = $eventData;
        unset($newValues['previous_attributes']);
        $newValues['changed_attributes'] = $changedAttributes;

        // Add metadata to new values
        $newValues = array_merge($newValues, [
            'event_id' => $event->getEventId(),
            'organization_id' => $event->getOrganizationId(),
            'occurred_at' => $event->getOccurredAt()->toDateTimeString(),
            'metadata' => $event->getMetadata(),
        ]);

        // Determine tags based on event type
        $tags = collect([
            'tms-event',
            explode('.', $event->getEventType())[0], // e.g., 'shipment', 'carrier'
        ])->filter()->implode(',');

        $triggeredBy = $event->getTriggeredBy();

        return Audit::create([
            'auditable_type' => $entityClass,
            'auditable_id' => $entityId,
            'event' => $event->getEventType(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()?->fullUrl(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'user_type' => $triggeredBy ? $triggeredBy::class : null,
            'user_id' => $triggeredBy?->id,
            'tags' => $tags,
        ]);
    }
}
