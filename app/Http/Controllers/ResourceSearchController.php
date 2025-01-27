<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;

abstract class ResourceSearchController extends Controller
{
    protected $model;

    public function search(ResourceSearchRequest $request)
    {
        $results = $this->model::search($request->input('query'))->get()->take(10);
        return response()->json($results);
    }
}
