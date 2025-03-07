<?php

namespace App\Actions\Contacts;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateContact
{
    use AsAction;

    public function handle(
        Contact $contact,
        string $name,
        string $contact_type,
        ?string $title,
        ?string $email,
        ?string $mobile_phone,
        ?string $office_phone,
        ?string $office_phone_extension,
    ): Contact
    {
        $contact->update([
            'title' => $title,
            'name' => $name,
            'email' => $email,
            'mobile_phone' => $mobile_phone,
            'office_phone' => $office_phone,
            'office_phone_extension' => $office_phone_extension,
            'contact_type' => $contact_type,
        ]);

        return $contact;
    }

    public function asController(ActionRequest $request, Contact $contact): Contact
    {

        $contact = $this->handle(
            contact: $contact,
            name: $request->validated('name'),
            contact_type: $request->validated('contact_type'),
            title: $request->validated('title'),
            email: $request->validated('email'),
            mobile_phone: $request->validated('mobile_phone'),
            office_phone: $request->validated('office_phone'),
            office_phone_extension: $request->validated('office_phone_extension'),
        );

        return $contact;
    }

    public function jsonResponse(Contact $contact)
    {
        return ContactResource::make($contact);
    }

    public function htmlResponse(Contact $contact)
    {
        return redirect()->back()->with('success', 'Contact updated successfully');
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'min:3', 'max:255'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_phone' => ['nullable', 'string', 'max:255', 'phone'],
            'office_phone' => ['nullable', 'string', 'max:255', 'phone'],
            'office_phone_extension' => ['nullable', 'string', 'max:255'],
            'contact_type' => ['required', 'string'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $contact = $request->route('contact');
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
