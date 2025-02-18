<?php

namespace Tests\Unit\Actions\Shipments;

use App\Actions\Shipments\CreateShipment;
use App\Actions\Shipments\UpdateShipmentStops;
use App\Actions\Shipments\UpdateShipmentNumber;
use App\Actions\Shipments\UpdateShipmentGeneral;
use App\Actions\Shipments\UpdateShipmentCustomers;
use App\Actions\Shipments\UpdateShipmentCarrierDetails;
use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Customers\Customer;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\TrailerType;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->customer = Customer::factory()->create();
    $this->carrier = Carrier::factory()->create();
    $this->facility = Facility::factory()->create();
    $this->trailerType = TrailerType::factory()->create();
});

test('it creates basic shipment with required fields', function () {
    $stops = [
        [
            'facility_id' => $this->facility->id,
            'stop_type' => 'pickup',
            'stop_number' => 1,
            'special_instructions' => 'Handle with care',
            'reference_numbers' => 'REF123,REF456',
            'appointment_at' => now()->addDays(2)->toDateTimeString(),
        ],
    ];

    // Act
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: $stops,
    );

    // Assert
    expect($shipment)->toBeInstanceOf(Shipment::class);
    
    expect(DB::table('shipments')->where([
        'id' => $shipment->id,
        'carrier_id' => $this->carrier->id,
    ])->exists())->toBeTrue();

    expect(DB::table('shipment_stops')->where([
        'shipment_id' => $shipment->id,
        'facility_id' => $this->facility->id,
        'stop_type' => 'pickup',
        'stop_number' => 1,
        'special_instructions' => 'Handle with care',
        'reference_numbers' => 'REF123,REF456',
    ])->exists())->toBeTrue();

    expect($shipment->stops)->toHaveCount(1)
        ->first()->appointment_at->not->toBeNull();
});

test('it creates shipment with all optional fields', function () {
    $stops = [
        [
            'facility_id' => $this->facility->id,
            'stop_type' => 'pickup',
            'stop_number' => 1,
            'special_instructions' => 'Handle with care',
            'reference_numbers' => 'REF123',
            'appointment_at' => now()->addDays(2)->toDateTimeString(),
        ],
    ];

    // Act
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: $stops,
        weight: 1000.5,
        tripDistance: 500.75,
        trailerTypeId: $this->trailerType->id,
        trailerTemperatureRange: true,
        trailerTemperature: 35.5,
        trailerTemperatureMaximum: 40.0,
    );

    // Assert
    expect($shipment)->toBeInstanceOf(Shipment::class);
    
    expect(DB::table('shipments')->where([
        'id' => $shipment->id,
        'carrier_id' => $this->carrier->id,
        'weight' => 1000.5,
        'trip_distance' => 500.75,
        'trailer_type_id' => $this->trailerType->id,
        'trailer_temperature_range' => true,
        'trailer_temperature' => 35.5,
        'trailer_temperature_maximum' => 40.0,
    ])->exists())->toBeTrue();
});

test('it creates shipment with multiple stops', function () {
    $pickup = $this->facility;
    $delivery = Facility::factory()->create();

    $stops = [
        [
            'facility_id' => $pickup->id,
            'stop_type' => 'pickup',
            'stop_number' => 1,
            'special_instructions' => 'First pickup',
            'reference_numbers' => 'REF123',
            'appointment_at' => now()->addDays(1)->toDateTimeString(),
        ],
        [
            'facility_id' => $delivery->id,
            'stop_type' => 'delivery',
            'stop_number' => 2,
            'special_instructions' => 'Final delivery',
            'reference_numbers' => 'REF456',
            'appointment_at' => now()->addDays(2)->toDateTimeString(),
        ],
    ];

    // Act
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: $stops,
    );

    // Assert
    expect($shipment->stops)->toHaveCount(2);

    expect(DB::table('shipment_stops')->where([
        'shipment_id' => $shipment->id,
        'facility_id' => $pickup->id,
        'stop_type' => 'pickup',
        'stop_number' => 1,
    ])->exists())->toBeTrue();

    expect(DB::table('shipment_stops')->where([
        'shipment_id' => $shipment->id,
        'facility_id' => $delivery->id,
        'stop_type' => 'delivery',
        'stop_number' => 2,
    ])->exists())->toBeTrue();
});

test('it updates shipment stops', function () {
    // Create initial shipment with stops
    $stops = [
        [
            'facility_id' => $this->facility->id,
            'stop_type' => 'pickup',
            'stop_number' => 1,
            'special_instructions' => 'Initial instructions',
            'reference_numbers' => 'REF123',
            'appointment_at' => now()->addDays(1)->toDateTimeString(),
        ],
    ];

    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: $stops,
    );

    $newFacility = Facility::factory()->create();
    $updatedStops = [
        [
            'id' => $shipment->stops->first()->id,
            'facility_id' => $newFacility->id,
            'stop_type' => 'delivery',
            'stop_number' => 1,
            'special_instructions' => 'Updated instructions',
            'reference_numbers' => 'REF456',
            'appointment_at' => now()->addDays(2)->toDateTimeString(),
        ],
    ];

    // Act
    $updatedShipment = UpdateShipmentStops::run($shipment, $updatedStops);

    // Assert
    expect($updatedShipment->stops)->toHaveCount(1);
    expect(DB::table('shipment_stops')->where([
        'shipment_id' => $shipment->id,
        'facility_id' => $newFacility->id,
        'stop_type' => 'delivery',
        'special_instructions' => 'Updated instructions',
        'reference_numbers' => 'REF456',
    ])->exists())->toBeTrue();
});

test('it updates shipment number', function () {
    // Create initial shipment
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->facility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Special instructions',
                'reference_numbers' => 'REF456',
                'appointment_at' => now()->addDay()->toDateTimeString(),
            ],
        ],
    );

    // Act
    $updatedShipment = UpdateShipmentNumber::run($shipment, 'SHIP-123');

    // Assert
    expect($updatedShipment->shipment_number)->toBe('SHIP-123');
    expect(DB::table('shipments')->where([
        'id' => $shipment->id,
        'shipment_number' => 'SHIP-123',
    ])->exists())->toBeTrue();
});

test('it updates shipment general details', function () {
    // Create initial shipment
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->facility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Special instructions',
                'reference_numbers' => 'REF456',
                'appointment_at' => now()->addDay()->toDateTimeString(),
            ],
        ],
    );

    // Act
    $updatedShipment = UpdateShipmentGeneral::run(
        shipment: $shipment,
        weight: 2000.5,
        tripDistance: 750.25,
        trailerTypeId: $this->trailerType->id,
        trailerTemperature: 45.5,
        trailerTemperatureMaximum: 50.0,
        trailerTemperatureRange: true,
    );

    // Assert
    expect(DB::table('shipments')->where([
        'id' => $shipment->id,
        'weight' => 2000.5,
        'trip_distance' => 750.25,
        'trailer_type_id' => $this->trailerType->id,
        'trailer_temperature' => 45.5,
        'trailer_temperature_maximum' => 50.0,
        'trailer_temperature_range' => true,
    ])->exists())->toBeTrue();
});

test('it updates shipment customers', function () {
    // Create initial shipment
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->facility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Special instructions',
                'reference_numbers' => 'REF456',
                'appointment_at' => now()->addDay()->toDateTimeString(),
            ],
        ],
    );

    $newCustomer = Customer::factory()->create();

    // Act
    $updatedShipment = UpdateShipmentCustomers::run(
        shipment: $shipment,
        customerIds: [$this->customer->id, $newCustomer->id],
    );

    // Assert
    expect($updatedShipment->customers)->toHaveCount(2)
        ->pluck('id')->toContain($this->customer->id, $newCustomer->id);
});

test('it updates shipment carrier details', function () {
    // Create initial shipment
    $shipment = CreateShipment::run(
        customerIds: [$this->customer->id],
        carrierId: $this->carrier->id,
        stops: [
            [
                'facility_id' => $this->facility->id,
                'stop_type' => 'pickup',
                'stop_number' => 1,
                'special_instructions' => 'Special instructions',
                'reference_numbers' => 'REF456',
                'appointment_at' => now()->addDay()->toDateTimeString(),
            ],
        ],
    );

    $newCarrier = Carrier::factory()->create();

    // Act
    $updatedShipment = UpdateShipmentCarrierDetails::run(
        shipment: $shipment,
        carrierId: $newCarrier->id,
    );

    // Assert
    expect($updatedShipment->carrier_id)->toBe($newCarrier->id);
    expect(DB::table('shipments')->where([
        'id' => $shipment->id,
        'carrier_id' => $newCarrier->id,
    ])->exists())->toBeTrue();
}); 