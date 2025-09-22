# Plugin-Friendly Event Foundations

The following changes were introduced to make the event system extensible before external plugins rely on it. Doing them now keeps the public API stable and avoids future breaking changes.

## Stable Event Contract

- Added `App\Contracts\Events\TmsEventContract` and made `App\Events\Core\TmsEvent` implement it.
- All core listeners/notifications type-hint the contract rather than the abstract base class.
- Future plugins can emit events by implementing the contract without inheriting framework traits, and the core can evolve the base class without changing the public surface.

## Central Event Registry

- Registered `App\Support\Events\TmsEventRegistry` as a singleton that stores listener mappings, auditable event types, and metrics handlers.
- Created `config/tms_events.php` with default registrations and helper methods (`registerListener`, `registerAuditableEvent`, `registerMetricsHandler`).
- Plugins can call into the registry during boot to append listeners or declare new events without mutating core config files.

## Dynamic Listener Wiring

- `App\Providers\EventServiceProvider` now consumes the registry instead of a fixed `$listen` array.
- Listener discovery is data-driven, so new packages can contribute listeners by registering them with the registry (or by merging config) without editing the provider.

## Configurable Audit & Metrics Hooks

- `AuditListener` and `MetricsListener` read their responsibilities from the registry instead of private arrays/switch statements.
- Plugins can flag new event types as auditable or supply custom metrics handlers with a single registration call.

## Consistent Event Metadata Access

- Listeners now rely on the contract methods (`getEventId()`, `getOrganizationId()`, `getOccurredAt()`, etc.) instead of concrete properties.
- Keeps custom event implementations interchangeable and ensures shared infrastructure (audit logs, notifications, metrics) stays compatible even if events are emitted from outside the core codebase.

These foundations let the platform accept third-party event emitters/listeners without additional breaking changes while keeping today’s behaviour intact.

## Plugin Usage Examples

Third-party packages can both publish new domain events and react to existing core events by leaning on the registry and standard Laravel tooling.

### Dispatching Plugin Events

Example event class (e.g., `packages/acme-inventory/src/Events/InventoryAdjusted.php`):

```php
namespace Acme\Inventory\Events;

use App\Events\Core\TmsEvent;
use Illuminate\Support\Str;

class InventoryAdjusted extends TmsEvent
{
    public function __construct(
        public readonly string $sku,
        public readonly int $newQuantity,
        public readonly int $organizationId,
        array $metadata = []
    ) {
        parent::__construct(
            eventId: Str::uuid()->toString(),
            organizationId: $organizationId,
            occurredAt: now(),
            triggeredBy: auth()->user(),
            metadata: $metadata
        );
    }

    public function getEventType(): string
    {
        return 'inventory.adjusted';
    }

    public function getEventData(): array
    {
        return [
            'sku' => $this->sku,
            'new_quantity' => $this->newQuantity,
        ];
    }
}
```

Bootstrap registrations in your plugin service provider (e.g., `packages/acme-inventory/src/InventoryServiceProvider.php`):

```php
use Acme\Inventory\Events\InventoryAdjusted;
use App\Support\Events\TmsEventRegistry;

public function boot(TmsEventRegistry $registry): void
{
    $registry->registerAuditableEvent('inventory.adjusted');
    $registry->registerMetricsHandler('inventory.adjusted', 'recordInventoryAdjustment');
}
```

Dispatch the event from domain logic (e.g., `packages/acme-inventory/src/Services/InventorySyncService.php`):

```php
event(new InventoryAdjusted($sku, $newQuantity, $organizationId, [
    'adjusted_via' => 'acme_inventory_sync',
]));
```

`recordInventoryAdjustment` should refer to a callable exposed by whichever metrics listener you register (for example, by adding the method to `App\Listeners\Events\MetricsListener`).

### Listening To Core Events

Register listeners in the plugin provider (e.g., `packages/acme-inventory/src/InventoryServiceProvider.php`):

```php
use App\Events\Shipments\ShipmentUpdated;
use App\Support\Events\TmsEventRegistry;
use Acme\Inventory\Listeners\SyncShipmentSkus;

public function boot(TmsEventRegistry $registry): void
{
    $registry->registerListener(ShipmentUpdated::class, SyncShipmentSkus::class);
}
```

Implement the listener (e.g., `packages/acme-inventory/src/Listeners/SyncShipmentSkus.php`):

```php
namespace Acme\Inventory\Listeners;

use App\Events\Shipments\ShipmentUpdated;

class SyncShipmentSkus
{
    public function handle(ShipmentUpdated $event): void
    {
        // Inspect $event->changedAttributes to determine whether SKUs changed
        // and push updates to the inventory system.
    }
}
```

Because everything runs through the shared registry, plugin events show up in the same audit/metrics/notification pipelines as first-party events when registered, and core events remain discoverable to any package listening in.
