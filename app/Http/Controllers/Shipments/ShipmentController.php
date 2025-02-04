<?php

namespace App\Http\Controllers\Shipments;

use App\Actions\Shipments\CreateShipment;
use App\Actions\Shipments\UpdateShipmentNumber;
use App\Http\Requests\Shipments\StoreShipmentRequest;
use App\Http\Requests\Shipments\UpdateShipmentRequest;
use App\Models\Shipments\Shipment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResourceSearchController;
use App\Http\Requests\ResourceSearchRequest;
use App\Http\Resources\NoteResource;
use App\Http\Resources\ShipmentResource;
use App\Http\Resources\ShipmentStopResource;
use App\Http\Resources\TrailerTypeResource;
use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Shipments\TrailerType;
use App\Models\Shipper;
use Inertia\Inertia;

class ShipmentController extends ResourceSearchController
{
    protected $model = Shipment::class;
    protected $modelResource = ShipmentResource::class;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Shipments/Index', [
            'shipments' => Shipment::all(),
        ]);  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Shipments/Create', [
            'trailerTypes' => TrailerTypeResource::collection(TrailerType::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShipmentRequest $request)
    {
        $shipment = CreateShipment::run(
            shipperIds: $request->shipper_ids,
            carrierId: $request->carrier_id,
            stops: $request->stops,
            weight: $request->weight,
            tripDistance: $request->trip_distance,
            trailerTypeId: $request->trailer_type_id,
            trailerTemperatureRange: $request->trailer_temperature_range,
            trailerTemperature: $request->trailer_temperature,
            trailerTemperatureMaximum: $request->trailer_temperature_maximum,
            shipmentNumber: $request->shipment_number,
        );

        return redirect()->route('shipments.show', $shipment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        return Inertia::render('Shipments/Show', [
            'shipment' => $shipment->load('carrier', 'shippers'),
            'stops' => ShipmentStopResource::collection($shipment->stops->load('facility')),
            'notes' => Inertia::defer(fn () => NoteResource::collection($shipment->notes->load('user'))),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        //
    }
}
