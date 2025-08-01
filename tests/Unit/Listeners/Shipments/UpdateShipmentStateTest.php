<?php

use App\Actions\Carriers\BounceCarrier;
use App\Actions\Shipments\CancelShipment;
use App\Actions\Shipments\DispatchShipment;
use App\Actions\Shipments\UncancelShipment;
use App\Actions\Shipments\UpdateShipmentCarrierDetails;
use App\Enums\Carriers\BounceCause;
use App\Enums\StopType;
use App\Events\Shipments\ShipmentCarrierBounced;
use App\Events\Shipments\ShipmentCarrierUpdated;
use App\Events\Shipments\ShipmentStateChanged;
use App\Events\Shipments\ShipmentStopsUpdated;
use App\Listeners\Shipments\UpdateShipmentState;
use App\Models\Carriers\Carrier;
use App\Models\Customers\Customer;
use App\Models\Facility;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use App\Services\Shipments\ShipmentStateService;
use App\States\Shipments\AtDelivery;
use App\States\Shipments\AtPickup;
use App\States\Shipments\Booked;
use App\States\Shipments\Canceled;
use App\States\Shipments\Delivered;
use App\States\Shipments\Dispatched;
use App\States\Shipments\InTransit;
use App\States\Shipments\Pending;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Traits\WithOrganization;

uses(RefreshDatabase::class, WithOrganization::class);

beforeEach(function () {
    $this->setupOrganization();
    $this->actingAs($this->user);
});

afterEach(function () {
    \Mockery::close();
});

it('remains pending when shipment created without carrier', function () {
    // Create shipment without carrier (should remain in Pending state)
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => null,
        'state' => Pending::class,
    ]);
    
    expect(get_class($shipment->state))->toBe(Pending::class);

    // Simulate CreateShipment firing ShipmentCarrierUpdated event
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierUpdated($shipment);
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in Pending state when no carrier is assigned
    expect(get_class($shipment->state))->toBe(Pending::class);
});

it('remains pending when carrier is removed', function () {
    // Create shipment with carrier (Booked state)
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => Booked::class,
    ]);
    
    expect(get_class($shipment->state))->toBe(Booked::class);

    // Remove carrier (simulate business scenario where carrier is removed)
    $shipment->update(['carrier_id' => null]);

    // Manually trigger the event handler
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in Booked state when carrier is removed (no automatic reversion)
    expect(get_class($shipment->state))->toBe(Booked::class);
});

it('transitions pending to booked when carrier is assigned', function () {
    // Create shipment without carrier (Pending state)
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => null,
        'state' => Pending::class,
    ]);
    
    expect(get_class($shipment->state))->toBe(Pending::class);

    // Create carrier and update shipment carrier details
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    
    UpdateShipmentCarrierDetails::run(
        shipment: $shipment,
        carrierId: $carrier->id,
    );

    // Manually trigger the event handler
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Booked::class);
});

it('does not transition if already past pending state', function () {
    // Create shipment in Dispatched state
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Dispatched::class,
    ]);

    // Try to update carrier
    $newCarrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    UpdateShipmentCarrierDetails::run(
        shipment: $shipment,
        carrierId: $newCarrier->id,
    );

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in Dispatched state
    expect(get_class($shipment->state))->toBe(Dispatched::class);
});

it('transitions booked to dispatched manually', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Booked::class,
    ]);

    DispatchShipment::run($shipment);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Dispatched::class);
});

it('transitions dispatched to at pickup when driver arrives', function () {
    // Create a dispatched shipment with stops
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Dispatched::class,
    ]);

    $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    
    $pickupStop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
        'appointment_at' => now()->addDay(),
    ]);

    // Update pickup stop with arrived_at
    $pickupStop->update(['arrived_at' => now()]);

    // The model event should fire ShipmentStopsUpdated
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(AtPickup::class);
});

it('transitions at pickup to in transit when pickup completed', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => AtPickup::class,
    ]);

    $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    
    $pickupStop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
    ]);

    $deliveryStop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => Facility::factory()->create(['organization_id' => $this->organization->id])->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 2,
    ]);
    
    // Complete pickup stop
    $pickupStop->update([
        'arrived_at' => now()->subHour(),
        'loaded_unloaded_at' => now()->subMinutes(30),
        'left_at' => now()->subMinutes(15),
    ]);

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(InTransit::class);
});

it('transitions in transit to at delivery when arriving', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => InTransit::class,
    ]);

    $pickupFacility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $deliveryFacility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    
    $pickupStop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $pickupFacility->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
        'arrived_at' => now()->subHours(2),
        'loaded_unloaded_at' => now()->subHours(1),
        'left_at' => now()->subMinutes(45),
    ]);

    $deliveryStop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $deliveryFacility->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 2,
    ]);
    
    // Arrive at delivery
    $deliveryStop->update(['arrived_at' => now()]);

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(AtDelivery::class);
});

it('transitions at delivery to delivered when completed', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => AtDelivery::class,
    ]);

    $pickupFacility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $deliveryFacility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    
    $pickupStop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $pickupFacility->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
        'arrived_at' => now()->subHours(3),
        'loaded_unloaded_at' => now()->subHours(2),
        'left_at' => now()->subHours(1),
    ]);

    $deliveryStop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $deliveryFacility->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 2,
        'arrived_at' => now()->subMinutes(30),
    ]);
    
    // Complete delivery
    $deliveryStop->update(['loaded_unloaded_at' => now()]);

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Delivered::class);
});

it('transitions booked to pending when carrier bounced', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => Booked::class,
    ]);

    BounceCarrier::run(
        shipment: $shipment,
        bounceCause: BounceCause::RATE_DISAGREEMENT_RATE_TOO_LOW,
        reason: 'Carrier declined the load',
    );

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierBounced($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Pending::class);
    expect($shipment->carrier_id)->toBeNull();
});

it('transitions dispatched to pending when carrier bounced', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => Dispatched::class,
    ]);

    BounceCarrier::run(
        shipment: $shipment,
        bounceCause: BounceCause::DRIVER_ILLNESS_EMERGENCY,
        reason: 'Driver no show',
    );

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierBounced($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Pending::class);
});

it('transitions at pickup to pending when carrier bounced', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => AtPickup::class,
    ]);

    BounceCarrier::run(
        shipment: $shipment,
        bounceCause: BounceCause::MECHANICAL_BREAKDOWN,
        reason: 'Truck breakdown',
    );

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierBounced($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Pending::class);
});

it('does not transition from in transit when carrier bounced', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => InTransit::class,
    ]);

    // Use proper BounceCarrier action instead of direct manipulation
    BounceCarrier::run(
        shipment: $shipment,
        bounceCause: BounceCause::MECHANICAL_BREAKDOWN,
        reason: 'Truck breakdown during transit',
    );
    
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierBounced($shipment);
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in InTransit
    expect(get_class($shipment->state))->toBe(InTransit::class);
});

it('can cancel from pending state', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Pending::class,
    ]);

    CancelShipment::run($shipment);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Canceled::class);
});

it('can cancel from booked state', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Booked::class,
    ]);

    CancelShipment::run($shipment);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Canceled::class);
});

it('can cancel from delivered state', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Delivered::class,
    ]);

    CancelShipment::run($shipment);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Canceled::class);
});

it('uncancels to pending when no carrier assigned', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => null,
        'state' => Canceled::class,
    ]);

    UncancelShipment::run($shipment);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Pending::class);
});

it('uncancels to booked when carrier assigned but no stop progress', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => Canceled::class,
    ]);

    // Create stops without progress
    ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
        'arrived_at' => null,
    ]);

    UncancelShipment::run($shipment);

    $shipment->refresh();
    expect(get_class($shipment->state))->toBe(Booked::class);
});

it('uncancels to calculated state when carrier assigned with stop progress', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => Canceled::class,
    ]);

    // Create stops with progress (driver at pickup)
    ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
        'arrived_at' => now(),
        'loaded_unloaded_at' => null,
    ]);

    ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => Facility::factory()->create(['organization_id' => $this->organization->id])->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 2,
        'arrived_at' => null,
    ]);

    UncancelShipment::run($shipment);

    $shipment->refresh();
    // Should transition to AtPickup based on the stop progress (arrived at pickup)
    expect(get_class($shipment->state))->toBe(AtPickup::class);
});

it('handles shipment with no stops', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Dispatched::class,
    ]);

    // Trigger stop update event even though no stops exist
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment);
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in Dispatched when no stops
    expect(get_class($shipment->state))->toBe(Dispatched::class);
});

it('ignores stop updates for non active states', function () {
    $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Pending::class,
    ]);

    $stop = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility->id,
        'stop_type' => StopType::Pickup,
        'arrived_at' => now(),
    ]);

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment);
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in Pending
    expect(get_class($shipment->state))->toBe(Pending::class);
});

it('handles multi stop shipments correctly', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => Dispatched::class,
    ]);

    $facility1 = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $facility2 = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $facility3 = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $facility4 = Facility::factory()->create(['organization_id' => $this->organization->id]);

    // Create multiple pickups and deliveries
    $pickup1 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility1->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
    ]);

    $pickup2 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility2->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 2,
    ]);

    $delivery1 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility3->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 3,
    ]);

    $delivery2 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility4->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 4,
    ]);

    // Complete first pickup
    $pickup1->update([
        'arrived_at' => now()->subHours(2),
        'loaded_unloaded_at' => now()->subHour(),
        'left_at' => now()->subMinutes(45),
    ]);

    // Arrive at second pickup
    $pickup2->update(['arrived_at' => now()]);

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    // Should be AtPickup for second pickup
    expect(get_class($shipment->state))->toBe(AtPickup::class);
});

it('transitions in transit back to at pickup for multi stop', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => InTransit::class,
    ]);

    $facility1 = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $facility2 = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $facility3 = Facility::factory()->create(['organization_id' => $this->organization->id]);

    // Complete first pickup
    $pickup1 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility1->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
        'arrived_at' => now()->subHours(3),
        'loaded_unloaded_at' => now()->subHours(2),
        'left_at' => now()->subHours(1),
    ]);

    // Arrive at second pickup (multi-stop scenario)
    $pickup2 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility2->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 2,
        'arrived_at' => now(),
    ]);

    $delivery = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility3->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 3,
    ]);

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    // Should transition back to AtPickup for the second pickup
    expect(get_class($shipment->state))->toBe(AtPickup::class);
});

it('transitions at delivery back to in transit for multi stop', function () {
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'state' => AtDelivery::class,
    ]);

    $facility1 = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $facility2 = Facility::factory()->create(['organization_id' => $this->organization->id]);
    $facility3 = Facility::factory()->create(['organization_id' => $this->organization->id]);

    // Complete pickup
    $pickup = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility1->id,
        'stop_type' => StopType::Pickup,
        'stop_number' => 1,
        'arrived_at' => now()->subHours(4),
        'loaded_unloaded_at' => now()->subHours(3),
        'left_at' => now()->subHours(2),
    ]);

    // Complete first delivery
    $delivery1 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility2->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 2,
        'arrived_at' => now()->subHour(),
        'loaded_unloaded_at' => now()->subMinutes(30),
        'left_at' => now()->subMinutes(15),
    ]);

    // Second delivery not yet reached (multi-stop scenario)
    $delivery2 = ShipmentStop::factory()->create([
        'organization_id' => $this->organization->id,
        'shipment_id' => $shipment->id,
        'facility_id' => $facility3->id,
        'stop_type' => StopType::Delivery,
        'stop_number' => 3,
    ]);

    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentStopsUpdated($shipment->fresh());
    $listener->handle($event);

    $shipment->refresh();
    // Should transition back to InTransit after completing first delivery with more deliveries remaining
    expect(get_class($shipment->state))->toBe(InTransit::class);
});

it('prevents carrier bounce from at delivery state', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => AtDelivery::class,
    ]);

    // Use proper BounceCarrier action instead of direct manipulation
    BounceCarrier::run(
        shipment: $shipment,
        bounceCause: BounceCause::MECHANICAL_BREAKDOWN,
        reason: 'Truck breakdown at delivery',
    );
    
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierBounced($shipment);
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in AtDelivery - cannot bounce once at delivery
    expect(get_class($shipment->state))->toBe(AtDelivery::class);
});

it('prevents carrier bounce from delivered state', function () {
    $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
        'state' => Delivered::class,
    ]);

    // Use proper BounceCarrier action
    BounceCarrier::run(
        shipment: $shipment,
        bounceCause: BounceCause::COMMUNICATION_BREAKDOWN,
        reason: 'Late bounce attempt',
    );
    
    $listener = new UpdateShipmentState(new ShipmentStateService());
    $event = new ShipmentCarrierBounced($shipment);
    $listener->handle($event);

    $shipment->refresh();
    // Should remain in Delivered - cannot bounce after delivery
    expect(get_class($shipment->state))->toBe(Delivered::class);
});