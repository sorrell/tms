<?php

namespace App\Actions\Contacts;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteContact
{
    use AsAction;

    public function handle(
        Contact $contact
    ): Contact
    {
        $contact->delete();
        return $contact;
    }

    public function asController(ActionRequest $request, Contact $contact): Contact
    {
        return $this->handle($contact);
    }

    public function jsonResponse(Contact $contact)
    {
        return ContactResource::make($contact);
    }

    public function htmlResponse(Contact $contact)
    {
        return redirect()->back()->with('success', 'Contact deleted successfully');
    }

    public function authorize(ActionRequest $request): bool
    {
        // Get the contact from the route parameter
        $contact = $request->route('contact');
        
        // Ensure we have a Contact model instance
        if (!$contact instanceof Contact) {
            $contact = Contact::find($contact);
        }
        
        $contactFor = $contact->contactFor;

        if (!$contactFor) {
            return false;
        }

        return $request->user()->can('update', $contactFor);
    }
}
