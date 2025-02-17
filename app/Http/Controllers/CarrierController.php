<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreCarrierRequest;
use App\Http\Requests\UpdateCarrierRequest;
use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarrierController extends ResourceSearchController
{
    protected $model = Carrier::class;
    protected $modelResource = CarrierResource::class;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'carrier_id' => 'nullable|exists:carriers,id',
        ]);

        if ($request->input('carrier_id')) {
            $carrier = Carrier::find($request->input('carrier_id'));
            return Inertia::render('Carriers/Index', [
                'carrier' => CarrierResource::make($carrier),
            ]);
        }

        return Inertia::render('Carriers/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrier $carrier)
    {
        return Inertia::render('Carriers/Show', [
            'carrier' => CarrierResource::make($carrier->load('physical_location', 'billing_location')),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrier $carrier)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrier $carrier)
    {
        //
    }
}
