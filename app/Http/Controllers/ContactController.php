<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;

class ContactController extends ResourceSearchController
{

    protected $model = Contact::class;
    protected $modelResource = ContactResource::class;
}
