<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacilityController extends ResourceSearchController
{
    protected $model = Facility::class;
    protected $modelResource = FacilityResource::class;

    public function index(Request $request)
    {
        $request->validate([
            'facility_id' => 'nullable|exists:facilities,id',
        ]);

        if ($request->input('facility_id')) {
            $facility = Facility::find($request->input('facility_id'));
            return Inertia::render('Facilities/Index', [
                'facility' => FacilityResource::make($facility),
            ]);
        }

        return Inertia::render('Facilities/Index');
    }
}
