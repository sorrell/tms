<?php

namespace App\Http\Controllers\Shipments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipments\StoreShipmentCustomerRequest;
use App\Http\Requests\Shipments\UpdateShipmentCustomerRequest;
use App\Models\Shipments\ShipmentCustomer;

class ShipmentCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShipmentCustomerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ShipmentCustomer $shipmentCustomer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShipmentCustomer $shipmentCustomer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipmentCustomerRequest $request, ShipmentCustomer $shipmentCustomer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShipmentCustomer $shipmentCustomer)
    {
        //
    }
}
