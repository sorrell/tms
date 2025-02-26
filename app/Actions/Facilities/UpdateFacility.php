<?php

namespace App\Actions\Facilities;

use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateFacility
{
    use AsAction;

    public function handle(
        Facility $facility,
        ?string $name = null,
        ?int $location_id = null,
    ): Facility
    {
        $facility->update(array_filter([
            'name' => $name,
            'location_id' => $location_id,
        ], fn($value) => !is_null($value)));

        return $facility;
    }

    public function asController(ActionRequest $request, Facility $facility): Facility
    {
        $facility = $this->handle(
            facility: $facility,
            name: $request->validated('name'),
            location_id: $request->validated('location_id'),
        );

        return $facility;
    }

    public function jsonResponse(Facility $facility)
    {
        return FacilityResource::make($facility);
    }

    public function htmlResponse(Facility $facility)
    {
        return redirect()->back()->with('success', 'Facility updated successfully');
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
        ];
    }
}