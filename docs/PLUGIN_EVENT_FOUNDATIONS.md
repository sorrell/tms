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
