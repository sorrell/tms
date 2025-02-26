<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FacilityController extends ResourceSearchController
{
    protected $model = Facility::class;
    protected $modelResource = FacilityResource::class;

    public function index(Request $request)
    {
        Gate::authorize(\App\Enums\Permission::FACILITY_VIEW);
        return Inertia::render('Facilities/Index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        Gate::authorize(\App\Enums\Permission::FACILITY_VIEW);
        return Inertia::render('Facilities/Show', [
            'facility' => FacilityResource::make($facility->load('location')),
        ]);
    }
}
