<?php

namespace Tests\Unit\Actions\Carriers;

use App\Actions\Carriers\CreateCarrier;
use App\Actions\Carriers\UpdateCarrierGeneral;
use App\Models\Carriers\Carrier;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->location = Location::factory()->create();
});

test('it creates basic carrier with required fields', function () {
    // Act
    $carrier = CreateCarrier::run(
        name: 'Test Carrier',
    );

    // Assert
    expect($carrier)->toBeInstanceOf(Carrier::class);
    
    expect(DB::table('carriers')->where([
        'id' => $carrier->id,
        'name' => 'Test Carrier',
    ])->exists())->toBeTrue();
});

test('it creates carrier with all optional fields', function () {
    // Act
    $carrier = CreateCarrier::run(
        name: 'Full Details Carrier',
    );

    // Act
    $updatedCarrier = UpdateCarrierGeneral::run(
        carrier: $carrier,
        name: 'Updated Carrier Name',
        mc_number: 'MC123456',
        dot_number: 'DOT789012',
        physical_location_id: $this->location->id,
        contact_email: 'contact@carrier.com',
        contact_phone: '(555) 123-4567',
    );

    // Assert
    expect($updatedCarrier)->toBeInstanceOf(Carrier::class);
    
    expect(DB::table('carriers')->where([
        'id' => $updatedCarrier->id,
        'name' => 'Updated Carrier Name',
        'mc_number' => 'MC123456',
        'dot_number' => 'DOT789012',
        'physical_location_id' => $this->location->id,
        'contact_email' => 'contact@carrier.com',
        'contact_phone' => '(555) 123-4567',
    ])->exists())->toBeTrue();
});

test('it updates carrier general details partially', function () {
    // Create initial carrier
    $carrier = CreateCarrier::run(
        name: 'Initial Carrier',
    );

    // Act - update only some fields
    $updatedCarrier = UpdateCarrierGeneral::run(
        carrier: $carrier,
        name: 'New Name',
        mc_number: 'MC999999',
    );

    // Assert
    expect(DB::table('carriers')->where([
        'id' => $carrier->id,
        'name' => 'New Name',
        'mc_number' => 'MC999999',
    ])->exists())->toBeTrue();

    // Verify other fields remain null
    expect($updatedCarrier->dot_number)->toBeNull();
    expect($updatedCarrier->physical_location_id)->toBeNull();
    expect($updatedCarrier->contact_email)->toBeNull();
    expect($updatedCarrier->contact_phone)->toBeNull();
});

test('it updates carrier contact information', function () {
    // Create initial carrier
    $carrier = CreateCarrier::run(
        name: 'Contact Test Carrier',
    );

    // Act
    $updatedCarrier = UpdateCarrierGeneral::run(
        carrier: $carrier,
        contact_email: 'new@carrier.com',
        contact_phone: '(555) 999-8888',
    );

    // Assert
    expect(DB::table('carriers')->where([
        'id' => $carrier->id,
        'contact_email' => 'new@carrier.com',
        'contact_phone' => '(555) 999-8888',
    ])->exists())->toBeTrue();
});

test('it updates carrier location', function () {
    // Create initial carrier
    $carrier = CreateCarrier::run(
        name: 'Location Test Carrier',
    );

    $newLocation = Location::factory()->create();

    // Act
    $updatedCarrier = UpdateCarrierGeneral::run(
        carrier: $carrier,
        physical_location_id: $newLocation->id,
    );

    // Assert
    expect($updatedCarrier->physical_location_id)->toBe($newLocation->id);
    expect(DB::table('carriers')->where([
        'id' => $carrier->id,
        'physical_location_id' => $newLocation->id,
    ])->exists())->toBeTrue();
}); 