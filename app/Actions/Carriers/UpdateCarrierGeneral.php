<?php

namespace App\Actions\Carriers;

use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCarrierGeneral
{
    use AsAction;

    public function handle(
        Carrier $carrier,
        ?string $name = null,
        ?string $mc_number = null,
        ?string $dot_number = null,
        ?int $physical_location_id = null,
        ?string $contact_email = null,
        ?string $contact_phone = null,
    ): Carrier
    {
        $carrier->update(array_filter([
            'name' => $name,
            'mc_number' => $mc_number,
            'dot_number' => $dot_number,
            'physical_location_id' => $physical_location_id,
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone,
        ], fn($value) => !is_null($value)));

        return $carrier;
    }

    public function asController(ActionRequest $request, Carrier $carrier): Carrier
    {

        $carrier = $this->handle(
            carrier: $carrier,
            name: $request->validated('name'),
            mc_number: $request->validated('mc_number'),
            dot_number: $request->validated('dot_number'),
            physical_location_id: $request->validated('physical_location_id'),
            contact_email: $request->validated('contact_email'),
            contact_phone: $request->validated('contact_phone'),
        );

        return $carrier;
    }

    public function jsonResponse(Carrier $carrier)
    {
        return CarrierResource::make($carrier);
    }

    public function htmlResponse(Carrier $carrier)
    {
        return redirect()->back()->with('success', 'Carrier updated successfully');
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'mc_number' => ['nullable', 'string'],
            'dot_number' => ['nullable', 'string'],
            'physical_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255', 'phone:US'],
        ];
    }
}
