<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchBoxRequest;
use App\Http\Requests\StoreShipperRequest;
use App\Http\Requests\UpdateShipperRequest;
use App\Models\Shipper;

class ShipperController extends Controller
{

    public function search(SearchBoxRequest $request)
    {
        if (!$request->input('query')) {
            return response()->json(Shipper::limit(10)->get());
        }

        $results = Shipper::where('name', 'like', '%' . $request->input('query') . '%')
            ->limit(10)
            ->get();

        return response()->json($results);
    }

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
