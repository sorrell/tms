<?php

namespace App\Http\Controllers;

use App\Enums\Contactable;
use App\Http\Requests\ResourceSearchRequest;
use App\Http\Resources\TimezoneResource;
use App\Models\Timezone;

class TimezoneController extends ResourceSearchController
{

    protected $model = Timezone::class;
    protected $modelResource = TimezoneResource::class;
    protected $modelSearchLimit = null;
}
