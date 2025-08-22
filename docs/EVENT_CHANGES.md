# Event System Review and Required Changes

## Scope
This document summarizes discrepancies and improvements identified while reviewing the current unstaged changes against `docs/EVENT_SYSTEM_IMPLEMENTATION.md`, and outlines proposed edits and tests to add.

---

## Priority Fixes

- Incorrect `ShipmentUpdated` construction
  - Issue: `App\Traits\DispatchesEvents::handleShipmentUpdated()` calls `new ShipmentUpdated($shipment, [ 'updated_via' => ..., 'changed_fields' => array_keys($changes) ])`, which passes metadata into the `changedAttributes` parameter and omits metadata entirely.
  - Fix: Pass `$changes = $shipment->getChanges()` directly as the second argument, and move metadata to the third argument.
    - Correct call: `event(new ShipmentUpdated($shipment, $changes, [ 'updated_via' => 'model_observer', 'has_carrier_change' => isset($changes['carrier_id']) ]));`

- Broadcasting channels not fully used
  - Issue: `App\Events\Core\BroadcastsToChannels::getBroadcastChannels()` builds per-entity channels but `broadcastOn()` returns only a single organization channel.
  - Fix: Make `broadcastOn()` return the full array from `getBroadcastChannels()` so entity-specific channels receive events.

- Missing event-listener mappings for some TMS events
  - Issue: `ShipmentCarrierBounced` (and any future TMS events) are not explicitly mapped in `App\Providers\EventServiceProvider`. The mapping for the abstract `TmsEvent::class` is unreliable for subclasses in Laravel.
  - Fix: Add explicit mappings for all concrete TMS events (e.g., `ShipmentCarrierBounced::class => [AuditListener::class, MetricsListener::class, NotificationListener::class]`). Consider removing the `TmsEvent::class` mapping to avoid confusion.

- `ShipmentStateChanged` not integrated with TMS listeners
  - Issue: `App\Events\Shipments\ShipmentStateChanged` extends Spatie’s `StateChanged` (not a `TmsEvent`), yet the guide/config include `shipment.state_changed` for auditing/notifications/metrics.
  - Fix:
     Register dedicated listeners for `ShipmentStateChanged` that perform audit/notification/metrics analogous to TMS listeners.

- Notification recipient filtering uses non-existent fields and columns
  - Issue: `NotificationListener` filters by `users.organization_id` and `users.notifications_enabled`, but:
    - Users table has `current_organization_id` (and org membership via pivot), not `organization_id`.
    - There is no `notifications_enabled` column.
  - Fix:
    - Filter users who are members of the event’s organization (via organizations pivot) or whose `current_organization_id` matches the event organization.
    - remove the `notifications_enabled` constraint 

- EventNotification text keys mismatch
  - Issue: `App\Notifications\EventNotification` uses `carrier.bounced` in subjects/descriptions, while the actual event is `shipment.carrier_bounced` and config uses the latter.
  - Fix: Replace `carrier.bounced` with `shipment.carrier_bounced` in subject/description maps.

---

## Additional Improvements

- Clean up unused variables
  - `UpdateShipmentGeneral`: remove `$originalAttributes` (unused).
  - `UpdateShipmentNumber`: remove `$previousNumber` (unused) or include in event metadata if we emit a number-changed event.

- Consolidate event mappings
  - Either rely entirely on explicit event mappings per concrete event class, or introduce robust discovery. Given `shouldDiscoverEvents()` is false, prefer explicit mappings and remove the `TmsEvent::class` mapping entry.

- Configuration utilization
  - `config/events.php` includes `retention_days` and `store_events`. Implement a scheduled cleanup job honoring `retention_days` to trim historic TMS event audits if desired.

---

## Tests To Add or Fix

- Fix existing test data typo
  - `tests/Feature/Events/AuditIntegrationTest.php`: expected `carrier_name` assertion uses different string than creation ("Metadata Test Carrier" vs. "Test Metadata Test Carrier"). Make expected name match creation input.

- ShipmentUpdated event payload
  - Add/adjust tests to ensure `changedAttributes` contains keys for changed fields and (ideally) their new values; verify metadata is separate and not mixed into `changedAttributes`.

- ShipmentCarrierBounced
  - Add tests that dispatch `ShipmentCarrierBounced` and assert:
    - Audit entry created with tags `tms-event,shipment`.
    - Notification is sent when event type is configured and user has permission.
    - Metrics updated (e.g., increment per-type counters) if we decide to track it.

- ShipmentStateChanged (once integrated into TMS)
  - Tests to verify:
    - Custom TMS event (or dedicated listeners) store audits with previous/current state.
    - Notifications are sent per config.
    - Metrics for state transitions increment and, for delivered state, delivery metrics insert occurs.

- Carrier events
  - Tests for `CarrierAssigned`, `CarrierUnassigned`, `CarrierCreated`, `CarrierStatusChanged`:
    - Audit storage, notification dispatch (only for configured types), and metrics updates as applicable.

- Notification recipients
  - Tests to ensure only users in the same organization (membership or current org) and with `RECEIVE_EVENT_NOTIFICATIONS` permission receive notifications. Add negative tests (no permission or different organization).

- Broadcasting
  - If broadcasting is enabled via config, test that events broadcast on organization AND entity-specific private channels per `BroadcastsToChannels`.

---

## Concrete Edit Checklist

- `App/Traits/DispatchesEvents.php`
  - Fix `ShipmentUpdated` construction to pass `$changes` as `changedAttributes` and move metadata to third param.

- `App/Providers/EventServiceProvider.php`
  - Add explicit listener mappings for all concrete TMS events, including `ShipmentCarrierBounced` (and any new ones added later). Consider removing the `TmsEvent::class` mapping.

- `App/Events/Core/BroadcastsToChannels.php`
  - Change `broadcastOn()` to return `array` of channels from `getBroadcastChannels()`.

- `App/Listeners/Events/NotificationListener.php`
  - Replace organization filter with membership or `current_organization_id` filter.
  - Remove `notifications_enabled` check (or add column and seed/feature-gate if keeping it).
  - Ensure permission filter matches new enum case `receive-event-notifications`.

- `App/Notifications/EventNotification.php`
  - Update subject/description keys from `carrier.bounced` to `shipment.carrier_bounced`.

- Docs
  - Update `docs/EVENT_SYSTEM_IMPLEMENTATION.md` to:
    - Reflect the current model observer approach

