<?php

namespace Tests\Unit\Actions\Shipments;

use App\Actions\Shipments\CreateShipment;
use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Organizations\Organization;
use App\Models\Shipper;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\TrailerType;
use App\Models\User;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;


beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::create([
        'name' => 'Test Organization',
        'owner_id' => $this->user->id,
    ]);
    Context::addHidden('current_organization_id', $this->organization->id);

    $this->shipper = Shipper::factory()->create();
    $this->carrier = Carrier::factory()->create();
    $this->facility = Facility::factory()->create();
    $this->trailerType = TrailerType::factory()->create();
    
    // $this->action = new CreateShipment();
});

test('it creates basic shipment with required fields', function () {
    $stops = [
        [
            'facility_id' => $this->facility->id,
            'stop_type' => 'pickup',
            'stop_number' => 1,
            'special_instructions' => 'Handle with care',
            'reference_numbers' => "['REF123', 'REF456']",
            'appointment' => [
                'datetime' => now()->addDays(2)->toDateTimeString(),
            ],
        ],
    ];

    // Act
    $shipment = CreateShipment::run(
        shipperIds: [$this->shipper->id],
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
    ])->exists())->toBeTrue();

    expect($shipment->stops)->toHaveCount(1)
        ->first()->appointment->not->toBeNull();
});

test('it creates shipment with all optional fields', function () {
    $stops = [
        [
            'facility_id' => $this->facility->id,
            'stop_type' => 'pickup',
            'stop_number' => 1,
            'special_instructions' => 'Handle with care',
            'reference_numbers' => 'REF123',
            'appointment' => [
                'datetime' => now()->addDays(2)->toDateTimeString(),
            ],
        ],
    ];

    // Act
    $shipment = CreateShipment::run(
        shipperIds: [$this->shipper->id],
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
            'appointment' => [
                'datetime' => now()->addDays(1)->toDateTimeString(),
            ],
        ],
        [
            'facility_id' => $delivery->id,
            'stop_type' => 'delivery',
            'stop_number' => 2,
            'special_instructions' => 'Final delivery',
            'reference_numbers' => 'REF456',
            'appointment' => [
                'datetime' => now()->addDays(2)->toDateTimeString(),
            ],
        ],
    ];

    // Act
    $shipment = CreateShipment::run(
        shipperIds: [$this->shipper->id],
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