<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;
class FacilityController extends Controller
{

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string',
        ]);

        if (!$request->input('query')) {
            return response()->json(Facility::with('location')->limit(10)->get());
        }

        $facilities = Facility::with('location');

        $facilities->orWhere('name', 'like', '%' . $request->input('query') . '%');
        $facilities->orWhereRelation('location', 'name', 'like', '%' . $request->input('query') . '%');
        $facilities->orWhereRelation('location', 'address_line_1', 'like', '%' . $request->input('query') . '%');
        $facilities->orWhereRelation('location', 'address_line_2', 'like', '%' . $request->input('query') . '%');
        $facilities->orWhereRelation('location', 'address_city', 'like', '%' . $request->input('query') . '%');
        $facilities->orWhereRelation('location', 'address_state', 'like', '%' . $request->input('query') . '%');
        $facilities->orWhereRelation('location', 'address_zipcode', 'like', '%' . $request->input('query') . '%');

        $facilities = $facilities->get();

        return response()->json($facilities);
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
    public function store(StoreFacilityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facility $facility)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        //
    }
}
