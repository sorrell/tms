<?php

namespace App\Http\Controllers;

use App\Enums\Contactable;
use App\Http\Requests\ResourceSearchRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;

class ContactController extends ResourceSearchController
{

    protected $model = Contact::class;
    protected $modelResource = ContactResource::class;

    public function search(ResourceSearchRequest $request)
    {
        $filters = $request->input('filters', []);
        
        // Converting the contact_for_type from enum to class name
        $transformedFilters = array_map(function ($filter) {
            if ($filter['name'] === 'contact_for_type') {
                $filter['value'] = Contactable::from($filter['value'])->getClassName();
            }
            return $filter;
        }, $filters);
        
        $request->merge(['filters' => $transformedFilters]);
        
        return parent::search($request);
    }
}
