# Event System Overview

This codebase provides a tenant-aware event pipeline that lets you publish TMS events, route them through shared listeners (audit, metrics, notifications), and extend behavior from plugins without touching core code. This guide walks through the components and how to work with them.

## Core Concepts

### TMS Event Contract

All dispatchable events implement `App\Contracts\Events\TmsEventContract`. The base class `App\Events\Core\TmsEvent` provides the common envelope:

```php
use App\Events\Core\TmsEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class ShipmentCreated extends TmsEvent implements ShouldBroadcast
{
    public function __construct(public readonly Shipment $shipment)
    {
        parent::__construct(
            eventId: Str::uuid()->toString(),
            organizationId: $shipment->organization_id,
            occurredAt: now(),
            triggeredBy: auth()->user(),
        );
    }

    public function getEventType(): string
    {
        return 'shipment.created';
    }

    public function getEventData(): array
    {
        return [
            'shipment_id' => $this->shipment->id,
            'shipment_number' => $this->shipment->shipment_number,
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}
```

Key points:
-
- `getEventType()` identifies the event as a string (`shipment.created`, `carrier.assigned`, etc.).
- `getEventData()` returns the payload consumed by listeners/notifications.
- `shouldBroadcast()` allows broadcasting events through Laravel’s broadcasting system.

### Broadcasting Helpers

The `App\Events\Core\BroadcastsToChannels` trait automatically registers broadcast channels based on the event envelope:
- Organization channel: `private-organization.{organizationId}`
- Entity channels (if present in payload): `shipment.{shipment_id}`, `carrier.{carrier_id}`, `customer.{customer_id}`, `user.{user_id}`

You can customize this by overriding `getEventData()` or extending the base observers for plugin-specific channels.

## Dispatching Events

Events are dispatched with Laravel’s `event()` helper:

```php
event(new ShipmentCreated($shipment));
```

Model observers wire lifecycle hooks to event dispatch. For example, `App\Observers\ShipmentObserver` fires `ShipmentCreated`, `ShipmentUpdated`, and related carrier events, while `App\Observers\CarrierObserver` handles carrier lifecycle events.

## Default Listeners

### Registry-driven Listener Binding

Listeners are stored in `App\Support\Events\TmsEventRegistry`. Default bindings live in `config/tms_events.php` so they can be extended via config merge or runtime registration.

Laravel wires listeners at boot (`app/Providers/EventServiceProvider.php`):

```php
$registry = app(TmsEventRegistry::class);
foreach ($registry->listeners() as $event => $listeners) {
    foreach ($listeners as $listener) {
        Event::listen($event, $listener);
    }
}
```

### Audit Listener

`App\Listeners\Events\AuditListener` writes events to the OwenIt audit log when their type appears in `tms_events.audit.tracked_events`. You can extend the list with:

```php
registry()->registerAuditableEvent('plugin.event_type');
```

### Metrics Listener

`App\Listeners\Events\MetricsListener` increments counters based on handlers in `tms_events.metrics.handlers`. Each handler points to a method on the listener.

```php
registry()->registerMetricsHandler('plugin.event_type', 'updatePluginMetrics');
```

### Notification Listener

`App\Listeners\Events\NotificationListener` sends `App\Notifications\EventNotification` for events listed in `config/events.php` under `notifiable`. Extend that config to include plugin events.

## Working with the Registry

Resolve the registry from the container (e.g., in a service provider):

```php
use App\Support\Events\TmsEventRegistry;

public function boot(TmsEventRegistry $registry)
{
    $registry->registerListener(MyEvent::class, MyListener::class);
    $registry->registerAuditableEvent('plugin.event_type');
    $registry->registerMetricsHandler('plugin.event_type', 'handlePluginMetric');
}
```

The registry merges additional listeners rather than replacing existing ones, allowing multiple packages to contribute to the same event.

## Adding a New Event

1. **Create the Event**: Extend `TmsEvent`, implement `getEventType/getEventData/shouldBroadcast`.
2. **Register Listeners**: Add to `config/tms_events.php` or call `registerListener()` from a service provider.
3. **Opt into Audit/Metrics**: Use the registry helpers to include the event in audit or metrics processing.
4. **Dispatch the Event**: Call `event(new MyEvent(...))` from your domain logic.

## Testing Tips

- Set `set_context_organization()` in tests to satisfy `HasOrganization` factories.
- Force the queue driver to `sync` so listeners run immediately:
  ```php
  config(['queue.default' => 'sync']);
  ```
- Use factories to build tenants, carriers, and shipments, ensuring they all share the same `organization_id`.

## Reference

- Contract: `app/Contracts/Events/TmsEventContract.php`
- Base event: `app/Events/Core/TmsEvent.php`
- Event registry: `app/Support/Events/TmsEventRegistry.php`
- Listener config: `config/tms_events.php`
- Default listeners: `app/Listeners/Events/{Audit,Metrics,Notification}Listener.php`
- Dispatch helpers: `app/Observers/ShipmentObserver.php`, `app/Observers/CarrierObserver.php`

With these pieces, both core developers and plugin authors can publish new event types, subscribe to them, and participate in auditing/metrics/notifications without modifying existing code.
