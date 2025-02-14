<?php

namespace App\Actions\Contacts;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateContact
{
    use AsAction;

    public function handle(
        string $name,
        ?string $title,
        ?string $email,
        ?string $mobile_phone,
        ?string $office_phone,
        ?string $office_phone_extension,
        Model $contactFor,
    ): Contact
    {
        return $contactFor->contacts()->create([
            'title' => $title,
            'name' => $name,
            'email' => $email,
            'mobile_phone' => $mobile_phone,
            'office_phone' => $office_phone,
            'office_phone_extension' => $office_phone_extension,
        ]);
    }

    public function asController(ActionRequest $request): Contact
    {
        $contactForClass = $request->validated('contact_for_type');
        $contactFor = $contactForClass::find($request->validated('contact_for_id'));

        $contact = $this->handle(
            name: $request->validated('name'),
            title: $request->validated('title'),
            email: $request->validated('email'),
            mobile_phone: $request->validated('mobile_phone'),
            office_phone: $request->validated('office_phone'),
            office_phone_extension: $request->validated('office_phone_extension'),
            contactFor: $contactFor,
        );

        return $contact;
    }

    public function jsonResponse(Contact $contact)
    {
        return ContactResource::make($contact);
    }

    public function htmlResponse(Contact $contact)
    {
        return redirect()->back()->with('success', 'Contact added successfully');
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'min:3', 'max:255'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_phone' => ['nullable', 'string', 'max:255', 'phone'],
            'office_phone' => ['nullable', 'string', 'max:255'],
            'office_phone_extension' => ['nullable', 'string', 'max:255'],
            'contact_for_id' => ['required'],
            'contact_for_type' => ['required', 'string'],
        ];
    }
}
