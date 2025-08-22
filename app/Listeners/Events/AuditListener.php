<?php

namespace App\Listeners\Events;

use App\Events\Core\TmsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class AuditListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * List of event types that should be tracked in the audit table
     */
    protected array $trackedEvents = [
        // Shipment events
        'shipment.created',
        'shipment.updated',
        'shipment.deleted',
        'shipment.restored',
        'shipment.state_changed',
        'shipment.carrier_bounced',
        
        // Carrier events
        'carrier.assigned',
        'carrier.unassigned',
        'carrier.created',
        'carrier.status_changed',
        
        // Financial events
        'payable.created',
        'payable.updated',
        'payable.deleted',
        'receivable.created',
        'receivable.updated',
        'receivable.deleted',
        
        // Document events
        'document.uploaded',
        'document.deleted',
        'document.expired',
        
        // Compliance events
        'user.permission_changed',
        'organization.settings_changed',
    ];

    public function handle(TmsEvent $event): void
    {
        try {
            // Only track events that are in our tracked list
            if (!$this->shouldTrackEvent($event)) {
                return;
            }

            // Store event as custom audit entry in OwenIT auditing system
            $audit = $this->createCustomAudit($event);

            // Log the event
            Log::channel('audit')->info('Event tracked in audit system', [
                'audit_id' => $audit->id,
                'event_id' => $event->eventId,
                'event_type' => $event->getEventType(),
                'organization_id' => $event->organizationId,
                'triggered_by' => $event->triggeredBy?->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to audit event', [
                'event_id' => $event->eventId,
                'event_type' => $event->getEventType(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function shouldTrackEvent(TmsEvent $event): bool
    {
        return in_array($event->getEventType(), $this->trackedEvents);
    }

    protected function createCustomAudit(TmsEvent $event): Audit
    {
        $eventData = $event->getEventData();
        $entityClass = $eventData['entity_type'] ?? null;
        $entityId = $eventData['entity_id'] ?? null;

        // Prepare old/new values based on event type
        $oldValues = [];
        $newValues = $eventData;

        // For update events, separate old and new values
        if (str_contains($event->getEventType(), '.updated') && isset($eventData['changed_attributes'])) {
            $oldValues = $eventData['changed_attributes'] ?? [];
            unset($newValues['changed_attributes']);
        }

        // Add metadata to new values
        $newValues = array_merge($newValues, [
            'event_id' => $event->eventId,
            'organization_id' => $event->organizationId,
            'occurred_at' => $event->occurredAt->toDateTimeString(),
            'metadata' => $event->metadata,
        ]);

        // Determine tags based on event type
        $tags = collect([
            'tms-event',
            explode('.', $event->getEventType())[0], // e.g., 'shipment', 'carrier'
        ])->filter()->implode(',');

        return Audit::create([
            'auditable_type' => $entityClass,
            'auditable_id' => $entityId,
            'event' => $event->getEventType(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()?->fullUrl(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'user_type' => $event->triggeredBy ? get_class($event->triggeredBy) : null,
            'user_id' => $event->triggeredBy?->id,
            'tags' => $tags,
        ]);
    }
}