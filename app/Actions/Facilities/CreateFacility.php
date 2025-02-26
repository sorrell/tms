<?php

namespace App\Actions\Facilities;

use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateFacility
{
    use AsAction;

    public function handle(string $name, int $location_id): Facility
    {
        return Facility::create([
            'name' => $name,
            'location_id' => $location_id,
        ]);
    }

    public function asController(ActionRequest $request) : Facility
    {
        return $this->handle(
            name: $request->validated('name'),
            location_id: $request->validated('location_id'),
        );
    }

    public function htmlResponse(Facility $facility)
    {
        return redirect()->route('facilities.show', $facility);
    }

    public function jsonResponse(Facility $facility) : FacilityResource
    {
        return FacilityResource::make($facility);
    }

    public function rules() : array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'location_id' => ['required', 'exists:locations,id'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::FACILITY_EDIT);
    }
}
