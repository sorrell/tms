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
        string $mc_number,
        string $dot_number,
        int $physical_location_id,
        string $contact_email,
        string $contact_phone,
    ): Carrier
    {
        $carrier->update([
            'mc_number' => $mc_number,
            'dot_number' => $dot_number,
            'physical_location_id' => $physical_location_id,
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone,
        ]);

        return $carrier;
    }

    public function asController(ActionRequest $request, Carrier $carrier): Carrier
    {

        $carrier = $this->handle(
            carrier: $carrier,
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
            'mc_number' => ['nullable', 'string'],
            'dot_number' => ['nullable', 'string'],
            'physical_location_id' => ['required', 'integer', 'exists:locations,id'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255', 'phone:US'],
        ];
    }
}
