<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use Illuminate\Database\Eloquent\Builder;

abstract class ResourceSearchController extends Controller
{
    protected $model;
    protected $modelResource;

    public function searchQuery(Builder $query)
    {
        return $query;
    }


    public function search(ResourceSearchRequest $request)
    {
        $query = $this->model::search($request->input('query'))
                    ->query(
                        fn (Builder $eloquentQuery) => $this->searchQuery($eloquentQuery)
                    );

        // If ids are provided, we are searching for specific records
        if ($request->has('ids')) {
            $query = $query->query(
                fn (Builder $eloquentQuery) => $eloquentQuery->whereIn('id', $request->input('ids'))
            );
        } 

        $results = $query->get()->take(10);

        // If relationships are requested to be loaded
        if ($request->has('with') && is_array($request->input('with'))) {
            $results->load($request->input('with'));
        }
        
        return response()->json($this->modelResource::collection($results));
    }
}
