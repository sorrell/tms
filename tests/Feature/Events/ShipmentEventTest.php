<?php

namespace Tests\Feature\Events;

use App\Actions\Carriers\CreateCarrier;
use App\Actions\Shipments\CreateShipment;
use App\Actions\Shipments\UpdateShipmentCarrierDetails;
use App\Actions\Shipments\UpdateShipmentGeneral;
use App\Actions\Shipments\UpdateShipmentNumber;
use App\Events\Carriers\CarrierAssigned;
use App\Events\Carriers\CarrierCreated;
use App\Events\Shipments\ShipmentCreated;
use App\Events\Shipments\ShipmentUpdated;
use App\Models\Carriers\Carrier;
use App\Models\Customers\Customer;
use App\Models\Facilities\Facility;
use App\Models\Organizations\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ShipmentEventTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_fires_shipment_created_event_when_creating_shipment()
    {
        Event::fake();

        $customer = Customer::factory()->create(['organization_id' => $this->organization->id]);
        $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
        
        CreateShipment::run(
            customerIds: [$customer->id],
            stops: [
                [
                    'facility_id' => $facility->id,
                    'stop_type' => 'pickup',
                    'stop_number' => 1,
                    'special_instructions' => null,
                    'reference_numbers' => null,
                    'appointment_at' => now()->format('Y-m-d H:i:s'),
                ]
            ]
        );

        Event::assertDispatched(ShipmentCreated::class);
    }

    /** @test */
    public function it_fires_carrier_created_event_when_creating_carrier()
    {
        Event::fake();

        CreateCarrier::run('Test Carrier');

        Event::assertDispatched(CarrierCreated::class, function ($event) {
            return $event->carrier->name === 'Test Carrier';
        });
    }

    /** @test */
    public function it_fires_shipment_updated_event_when_updating_general_details()
    {
        Event::fake();

        $customer = Customer::factory()->create(['organization_id' => $this->organization->id]);
        $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
        
        $shipment = CreateShipment::run(
            customerIds: [$customer->id],
            stops: [
                [
                    'facility_id' => $facility->id,
                    'stop_type' => 'pickup',
                    'stop_number' => 1,
                    'special_instructions' => null,
                    'reference_numbers' => null,
                    'appointment_at' => now()->format('Y-m-d H:i:s'),
                ]
            ]
        );

        Event::fake(); // Reset events to focus on update

        UpdateShipmentGeneral::run(
            shipment: $shipment,
            weight: 1000.0,
            tripDistance: 500.0
        );

        Event::assertDispatched(ShipmentUpdated::class, function ($event) use ($shipment) {
            return $event->shipment->id === $shipment->id 
                && isset($event->changedAttributes['weight'])
                && isset($event->changedAttributes['trip_distance']);
        });
    }

    /** @test */
    public function it_fires_carrier_assigned_event_when_assigning_carrier()
    {
        Event::fake();

        $customer = Customer::factory()->create(['organization_id' => $this->organization->id]);
        $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
        $carrier = Carrier::factory()->create(['organization_id' => $this->organization->id]);
        
        $shipment = CreateShipment::run(
            customerIds: [$customer->id],
            stops: [
                [
                    'facility_id' => $facility->id,
                    'stop_type' => 'pickup',
                    'stop_number' => 1,
                    'special_instructions' => null,
                    'reference_numbers' => null,
                    'appointment_at' => now()->format('Y-m-d H:i:s'),
                ]
            ]
        );

        Event::fake(); // Reset events to focus on carrier assignment

        UpdateShipmentCarrierDetails::run(
            shipment: $shipment,
            carrierId: $carrier->id
        );

        Event::assertDispatched(CarrierAssigned::class, function ($event) use ($shipment, $carrier) {
            return $event->shipment->id === $shipment->id 
                && $event->carrier->id === $carrier->id;
        });
    }

    /** @test */
    public function it_fires_shipment_updated_event_when_updating_shipment_number()
    {
        Event::fake();

        $customer = Customer::factory()->create(['organization_id' => $this->organization->id]);
        $facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
        
        $shipment = CreateShipment::run(
            customerIds: [$customer->id],
            stops: [
                [
                    'facility_id' => $facility->id,
                    'stop_type' => 'pickup',
                    'stop_number' => 1,
                    'special_instructions' => null,
                    'reference_numbers' => null,
                    'appointment_at' => now()->format('Y-m-d H:i:s'),
                ]
            ]
        );

        Event::fake(); // Reset events to focus on update

        UpdateShipmentNumber::run(
            shipment: $shipment,
            shipmentNumber: 'TEST-123'
        );

        Event::assertDispatched(ShipmentUpdated::class, function ($event) use ($shipment) {
            return $event->shipment->id === $shipment->id 
                && isset($event->changedAttributes['shipment_number'])
                && $event->changedAttributes['shipment_number'] === 'TEST-123';
        });
    }
}