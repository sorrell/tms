<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;
class FacilityController extends ResourceSearchController
{
    protected $model = Facility::class;
    protected $modelResource = FacilityResource::class;
}
