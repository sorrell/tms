<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreShipperRequest;
use App\Http\Requests\UpdateShipperRequest;
use App\Http\Resources\ShipperResource;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipperController extends ResourceSearchController
{
    protected $model = Shipper::class;
    protected $modelResource = ShipperResource::class;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'shipper_id' => 'nullable|exists:shippers,id',
        ]);

        if ($request->input('shipper_id')) {
            $shipper = Shipper::find($request->input('shipper_id'));
            return Inertia::render('Shippers/Index', [
                'shipper' => ShipperResource::make($shipper),
            ]);
        }

        return Inertia::render('Shippers/Index');
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
    public function store(StoreShipperRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipper $shipper)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipper $shipper)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipperRequest $request, Shipper $shipper)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipper $shipper)
    {
        //
    }
}
