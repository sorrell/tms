<?php

use App\Actions\Shipments\UncancelShipment;
use App\Enums\Permission;
use App\Enums\StopType;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use App\Models\User;
use App\States\Shipments\AtDelivery;
use App\States\Shipments\AtPickup;
use App\States\Shipments\Booked;
use App\States\Shipments\Canceled;
use App\States\Shipments\Delivered;
use App\States\Shipments\Dispatched;
use App\States\Shipments\InTransit;
use App\States\Shipments\Pending;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lorisleiva\Actions\ActionRequest;
use Spatie\Permission\Models\Role;
use Tests\Traits\WithOrganization;

uses(RefreshDatabase::class, WithOrganization::class);

beforeEach(function () {
    $this->setupOrganization();
});

afterEach(function () {
    \Mockery::close();
});

it('can uncancel a shipment with no carrier to pending state', function () {
    // Create a shipment in Canceled state with no carrier
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => null,
    ]);
    $shipment->state->transitionTo(Canceled::class);
    expect(get_class($shipment->state))->toBe(Canceled::class);

    // Execute the uncancel action
    $action = new UncancelShipment();
    $result = $action->handle($shipment);

    // Assert the shipment state has changed to Pending
    // (No carrier shipments stay in Pending - no listener recalculation needed)
    expect(get_class($result->state))->toBe(Pending::class);
    expect($result->state->getValue())->toBe('pending');
});

it('can uncancel a shipment with carrier but no stop progress to booked state', function () {
    // Create a carrier
    $carrier = Carrier::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    // Create a shipment in Canceled state with a carrier but no stop progress
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
    ]);
    
    // Add stops with no progress
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 1,
        'stop_type' => StopType::Pickup,
        'arrived_at' => null,
        'loaded_unloaded_at' => null,
        'left_at' => null,
    ]);
    
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 2,
        'stop_type' => StopType::Delivery,
        'arrived_at' => null,
        'loaded_unloaded_at' => null,
        'left_at' => null,
    ]);

    $shipment->state->transitionTo(Canceled::class);
    expect(get_class($shipment->state))->toBe(Canceled::class);

    // Execute the uncancel action
    $action = new UncancelShipment();
    $result = $action->handle($shipment);

    // Assert the shipment state has changed to Booked
    // (Action transitions to Booked since no stop progress, doesn't auto-dispatch)
    expect(get_class($result->state))->toBe(Booked::class);
    expect($result->state->getValue())->toBe('booked');
});

it('can uncancel a shipment which listener recalculates to at pickup', function () {
    // Create a carrier
    $carrier = Carrier::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    // Create a shipment in Canceled state with a carrier and stop progress
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
    ]);
    
    // Add stops with pickup progress - currently at pickup (arrived but not left)
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 1,
        'stop_type' => StopType::Pickup,
        'arrived_at' => now(),
        'loaded_unloaded_at' => null,
        'left_at' => null,
    ]);
    
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 2,
        'stop_type' => StopType::Delivery,
        'arrived_at' => null,
        'loaded_unloaded_at' => null,
        'left_at' => null,
    ]);

    $shipment->state->transitionTo(Canceled::class);
    expect(get_class($shipment->state))->toBe(Canceled::class);

    // Execute the uncancel action
    $action = new UncancelShipment();
    $result = $action->handle($shipment);

    // Assert the shipment state has changed to AtPickup
    // (Action transitions to Booked, then state service recalculates to AtPickup based on stop progress)
    expect(get_class($result->state))->toBe(AtPickup::class);
    expect($result->state->getValue())->toBe('at_pickup');
});

it('can uncancel a shipment which listener recalculates to in transit', function () {
    // Create a carrier
    $carrier = Carrier::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    // Create a shipment in Canceled state with a carrier and stop progress
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
    ]);
    
    // Add stops with in-transit progress - pickup completed (arrived, loaded, and left)
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 1,
        'stop_type' => StopType::Pickup,
        'arrived_at' => now()->subHours(2),
        'loaded_unloaded_at' => now()->subHours(1.5),
        'left_at' => now()->subHours(1),
    ]);
    
    // Delivery not yet reached
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 2,
        'stop_type' => StopType::Delivery,
        'arrived_at' => null,
        'loaded_unloaded_at' => null,
        'left_at' => null,
    ]);

    $shipment->state->transitionTo(Canceled::class);
    expect(get_class($shipment->state))->toBe(Canceled::class);

    // Execute the uncancel action
    $action = new UncancelShipment();
    $result = $action->handle($shipment);

    // Assert the shipment state has changed to InTransit
    // (Action transitions to Booked, then state service recalculates to InTransit based on stop progress)
    expect(get_class($result->state))->toBe(InTransit::class);
    expect($result->state->getValue())->toBe('in_transit');
});

it('can uncancel a shipment which listener recalculates to at delivery', function () {
    // Create a carrier
    $carrier = Carrier::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    // Create a shipment in Canceled state with a carrier and stop progress
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
    ]);
    
    // Add stops with at-delivery progress - pickup completed
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 1,
        'stop_type' => StopType::Pickup,
        'arrived_at' => now()->subHours(3),
        'loaded_unloaded_at' => now()->subHours(2.5),
        'left_at' => now()->subHours(2),
    ]);
    
    // Currently at delivery (arrived but not left)
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 2,
        'stop_type' => StopType::Delivery,
        'arrived_at' => now()->subHour(),
        'loaded_unloaded_at' => null,
        'left_at' => null,
    ]);

    $shipment->state->transitionTo(Canceled::class);
    expect(get_class($shipment->state))->toBe(Canceled::class);

    // Execute the uncancel action
    $action = new UncancelShipment();
    $result = $action->handle($shipment);

    // Assert the shipment state has changed to AtDelivery
    // (Action transitions to Booked, then state service recalculates to AtDelivery based on stop progress)
    expect(get_class($result->state))->toBe(AtDelivery::class);
    expect($result->state->getValue())->toBe('at_delivery');
});

it('can uncancel a shipment which listener recalculates to delivered', function () {
    // Create a carrier
    $carrier = Carrier::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    // Create a shipment in Canceled state with a carrier and completed delivery
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
    ]);
    
    // Add stops with completed delivery
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 1,
        'stop_type' => StopType::Pickup,
        'arrived_at' => now()->subHours(4),
        'loaded_unloaded_at' => now()->subHours(3),
        'left_at' => now()->subHours(2.5),
    ]);
    
    ShipmentStop::factory()->create([
        'shipment_id' => $shipment->id,
        'stop_number' => 2,
        'stop_type' => StopType::Delivery,
        'arrived_at' => now()->subHours(2),
        'loaded_unloaded_at' => now()->subHour(),
        'left_at' => now()->subMinutes(30),
    ]);

    $shipment->state->transitionTo(Canceled::class);
    expect(get_class($shipment->state))->toBe(Canceled::class);

    // Execute the uncancel action
    $action = new UncancelShipment();
    $result = $action->handle($shipment);

    // Assert the shipment state has changed to Delivered
    // (Action transitions to Booked, then state service recalculates to Delivered based on stop progress)
    expect(get_class($result->state))->toBe(Delivered::class);
    expect($result->state->getValue())->toBe('delivered');
});

it('can uncancel a shipment that was only booked not dispatched', function () {
    // Create a carrier
    $carrier = Carrier::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    // Create a shipment in Canceled state with a carrier but NO stops at all
    // This represents a shipment that was only Booked (carrier assigned) but never Dispatched
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
        'carrier_id' => $carrier->id,
    ]);
    
    // First transition to Booked to simulate the real workflow
    $shipment->state->transitionTo(Booked::class);
    // Then cancel it
    $shipment->state->transitionTo(Canceled::class);
    expect(get_class($shipment->state))->toBe(Canceled::class);

    // Execute the uncancel action
    $action = new UncancelShipment();
    $result = $action->handle($shipment);

    // Assert the shipment state has changed to Booked
    // (Action transitions to Booked since no stop progress, doesn't auto-dispatch)
    expect(get_class($result->state))->toBe(Booked::class);
    expect($result->state->getValue())->toBe('booked');
});

it('requires shipment edit permission', function () {
    // Create a user without shipment edit permission
    $user = User::factory()->create();
    $this->organization->users()->attach($user);
    $role = Role::create(['name' => 'tester', 'organization_id' => $this->organization->id]);
    $user->assignRole($role);
    
    // Create a mock request with the unauthorized user
    $request = \Mockery::mock(ActionRequest::class);
    $request->shouldReceive('user')->andReturn($user);

    // Create the action and check authorization
    $action = new UncancelShipment();
    expect($action->authorize($request))->toBeFalse();

    // Now give the user permission and verify it works
    $role->givePermissionTo(Permission::SHIPMENT_EDIT);
    expect($action->authorize($request))->toBeTrue();
});

it('returns redirect response when called as controller', function () {
    // Create a shipment in Canceled state
    $shipment = Shipment::factory()->create([
        'organization_id' => $this->organization->id,
    ]);
    $shipment->state->transitionTo(Canceled::class);

    // Create a user with shipment edit permission
    $user = User::factory()->create();
    $this->organization->users()->attach($user);
    $role = Role::create(['name' => 'editor', 'organization_id' => $this->organization->id]);
    $role->givePermissionTo(Permission::SHIPMENT_EDIT);
    $user->assignRole($role);
    
    // Create a mock request with the authorized user
    $request = \Mockery::mock(ActionRequest::class);
    $request->shouldReceive('user')->andReturn($user);

    // Execute the action as controller
    $action = new UncancelShipment();
    $response = $action->asController($request, $shipment);

    // Assert the response is a redirect with success message
    expect($response->getContent())->toContain('Redirecting');
    expect($response->isRedirect())->toBeTrue();
});
