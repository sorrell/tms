<?php

namespace App\Actions\Contacts;

use App\Enums\Contactable;
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
        string $contact_type,
        ?string $title,
        ?string $email,
        ?string $mobile_phone,
        ?string $office_phone,
        ?string $office_phone_extension,
        Model $contactFor,
    ): Contact
    {
        return Contact::create([
            'contact_type' => $contact_type,
            'title' => $title,
            'name' => $name,
            'email' => $email,
            'mobile_phone' => $mobile_phone,
            'office_phone' => $office_phone,
            'office_phone_extension' => $office_phone_extension,
            'contact_for_id' => $contactFor->getKey(),
            'contact_for_type' => $contactFor->getMorphClass(),
        ]);
    }

    public function asController(ActionRequest $request): Contact
    {
        $contactForClass = Contactable::from(
            $request->validated('contact_for_type')
        )->getClassName();

        $contactFor = $contactForClass::find($request->validated('contact_for_id'));

        $contact = $this->handle(
            name: $request->validated('name'),
            contact_type: $request->validated('contact_type'),
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
            'contact_type' => ['required', 'string'],
            'title' => ['nullable', 'string', 'min:3', 'max:255'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_phone' => ['nullable', 'string', 'max:255', 'phone'],
            'office_phone' => ['nullable', 'string', 'max:255', 'phone'],
            'office_phone_extension' => ['nullable', 'string', 'max:255'],
            'contact_for_id' => ['required'],
            'contact_for_type' => ['required', 'string'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $contactForClass = Contactable::from(
            $request->input('contact_for_type')
        )->getClassName();

        $contactFor = $contactForClass::find($request->input('contact_for_id'));

        if (!$contactFor) {
            return false;
        }

        return $request->user()->can('update', $contactFor);
    }
}
