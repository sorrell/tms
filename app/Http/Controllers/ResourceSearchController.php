<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;

abstract class ResourceSearchController extends Controller
{
    protected $model;
    protected $resource;

    public function search(ResourceSearchRequest $request)
    {
        if ($request->has('ids')) {
            $query = $this->model::whereIn('id', $request->input('ids'))->limit(10);
            $results = $query->get();
        } else {
            $query = $this->model::search($request->input('query'));
            $results = $query->get()->take(10);
        }

        // If relationships are requested to be loaded
        if ($request->has('with') && is_array($request->input('with'))) {
            $results->load($request->input('with'));
        }
        
        return response()->json($this->resource::collection($results));
    }
}
