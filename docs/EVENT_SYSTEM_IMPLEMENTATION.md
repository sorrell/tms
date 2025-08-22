# Event System Implementation Guide

## Overview

This document provides implementation instructions for the LoadPartner TMS event system. The system integrates with the existing OwenIT Laravel Auditing package to provide comprehensive event tracking and audit trails.

## Completed Components

### 1. Core Infrastructure
- **TmsEvent Abstract Class** (`app/Events/Core/TmsEvent.php`): Base class for all events
- **BroadcastsToChannels Trait** (`app/Events/Core/BroadcastsToChannels.php`): Centralized broadcasting logic
- **OwenIT Auditing Integration**: Events are stored in the existing `audits` table as custom audit entries
- **Event Configuration** (`config/events.php`): Centralized event system configuration

### 2. Implemented Events

#### Shipment Events
- `ShipmentCreated`: Fired when a new shipment is created
- `ShipmentUpdated`: Fired when shipment details are modified
- `ShipmentDeleted`: Fired when a shipment is soft deleted
- `ShipmentRestored`: Fired when a shipment is restored

#### Carrier Events
- `CarrierAssigned`: Fired when a carrier is assigned to a shipment
- `CarrierUnassigned`: Fired when a carrier is removed from a shipment
- `CarrierCreated`: Fired when a new carrier is onboarded
- `CarrierStatusChanged`: Fired when carrier status changes

### 3. Event Listeners
- **NotificationListener**: Sends notifications for configured events
- **AuditListener**: Stores events as custom audit entries in OwenIT auditing system
- **MetricsListener**: Updates business metrics based on events

### 4. Supporting Components
- **EventNotification**: Notification class for event-based emails
- **EventServiceProvider**: Registers event-listener mappings

## Integration Steps

### 1. Events are automatically stored in the existing `audits` table
The system leverages the existing OwenIT auditing infrastructure, so no additional database setup is required.

### 2. Events are triggered directly from Actions
Events are fired directly from business logic actions rather than model observers, providing better control and context.

### 4. Configure Broadcasting (Optional)
If you want real-time updates, configure Laravel broadcasting in `config/broadcasting.php`.

### 5. Configure Event Notifications
Create a configuration file `config/events.php`:

```php
return [
    'notifiable' => [
        'shipment.created',
        'shipment.state_changed',
        'carrier.assigned',
        'carrier.bounced',
        'document.expired',
    ],
];
```

### 6. Add Notification Permission
Add a new permission for receiving event notifications:

```php
// In app/Enums/Permission.php
case RECEIVE_EVENT_NOTIFICATIONS = 'receive-event-notifications';

// In the label() method
self::RECEIVE_EVENT_NOTIFICATIONS => 'Receive Event Notifications',
```

Then create and run a migration to sync permissions:
```bash
sail artisan make:migration add_event_notification_permission
```

## Usage Examples

### Manually Dispatching Events

```php
use App\Events\Shipments\ShipmentCreated;

// When creating a shipment
$shipment = Shipment::create($data);
event(new ShipmentCreated($shipment, ['source' => 'api']));
```

### Creating New Events

When creating new events, simply extend `TmsEvent` and implement the required methods. Broadcasting is handled automatically:

```php
use App\Events\Core\TmsEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MyCustomEvent extends TmsEvent implements ShouldBroadcast
{
    public function getEventType(): string
    {
        return 'my.custom_event';
    }

    public function getEventData(): array
    {
        return [
            'entity_type' => MyModel::class,
            'entity_id' => $this->model->id,
            // ... other data
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}
```

### Querying Events from Audit System

```php
use OwenIt\Auditing\Models\Audit;

// Get all events for a specific shipment
$shipmentEvents = Audit::where('auditable_type', \App\Models\Shipments\Shipment::class)
    ->where('auditable_id', $shipment->id)
    ->where('tags', 'like', '%tms-event%')
    ->with('user')
    ->orderBy('created_at', 'desc')
    ->get();

// Get events by type
$carrierAssignedEvents = Audit::where('event', 'carrier.assigned')
    ->where('tags', 'like', '%tms-event%')
    ->with('user')
    ->get();

// Get events by entity type
$shipmentEvents = Audit::where('tags', 'like', '%shipment%')
    ->where('tags', 'like', '%tms-event%')
    ->get();

// Use existing audit history methods
$auditHistory = $shipment->audits()->with('user')->get();
```

### Listening to Specific Events

```php
// In a custom listener
use App\Events\Shipments\ShipmentStateChanged;

class CustomShipmentListener
{
    public function handle(ShipmentStateChanged $event)
    {
        if ($event->shipment->state->value() === 'delivered') {
            // Send delivery confirmation
        }
    }
}
```

## Remaining Implementation Tasks

### High Priority
1. Add tests for all event types and listeners
2. Configure broadcasting channels for real-time updates

### Medium Priority  
1. Implement Financial events (Payable/Receivable)
2. Implement Document events
3. Implement Communication events
4. Implement Customer events
5. Implement Stop/Location events

### Low Priority
1. Create event subscription settings UI
2. Implement webhook system
3. Create API endpoints for querying events

## Testing

Create tests for:
1. Event dispatching from models
2. Event storage in database
3. Listener execution
4. Notification sending
5. Metrics calculation

Example test:
```php
public function test_shipment_created_event_is_dispatched()
{
    Event::fake();
    
    $shipment = Shipment::factory()->create();
    
    Event::assertDispatched(ShipmentCreated::class, function ($event) use ($shipment) {
        return $event->shipment->id === $shipment->id;
    });
}
```

## Audit Integration Benefits

1. **Unified Audit Trail**: TMS events appear alongside regular model audits in the same `audits` table
2. **Existing UI Integration**: Events can be viewed through existing audit history endpoints
3. **Consistent Data Structure**: Events follow the same audit format as other system changes
4. **Tagging System**: Events are tagged as 'tms-event' and by entity type for easy filtering
5. **Selective Tracking**: Only specified event types are tracked to avoid audit table bloat

## Performance Considerations

1. All listeners implement `ShouldQueue` for async processing
2. Events are stored in the existing `audits` table with proper indexing
3. Metrics use Redis caching for performance
4. Leverages existing audit cleanup and retention policies

## Security Notes

1. Events respect organization boundaries and audit permissions
2. Notification recipients are filtered by permissions
3. Broadcasting channels use authentication
4. Sensitive data handling follows existing audit security patterns
5. Events include IP address, user agent, and user tracking like regular audits