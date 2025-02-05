<?php

namespace App\Actions\Locations;

use App\Enums\Notable;
use App\Http\Resources\LocationResource;
use App\Http\Resources\NoteResource;
use App\Models\Location;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLocation
{
    use AsAction;

    public function handle(
        string $address_line_1,
        string $address_city,
        string $address_state,
        string $address_zipcode,
        ?string $name = null,
        ?string $address_line_2 = null,
    ): Location
    {
        return Location::create([
            'name' => $name,
            'address_line_1' => $address_line_1,
            'address_line_2' => $address_line_2,
            'address_city' => $address_city,
            'address_state' => $address_state,
            'address_zipcode' => $address_zipcode,
        ]);
        
    }

    public function asController(ActionRequest $request): Location
    {
        $location = $this->handle(
            $request->validated('name'),
            $request->validated('address_line_1'),
            $request->validated('address_line_2'),
            $request->validated('address_city'),
            $request->validated('address_state'),
            $request->validated('address_zipcode'),
        );

        return $location;
    }

    public function jsonResponse(Location $location)
    {
        return LocationResource::make($location);
    }

    public function htmlResponse(Location $location)
    {
        return redirect()->back()->with('success', 'Location added successfully');
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
            'address_line_1' => ['required', 'string', 'min:3', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'min:3', 'max:255'],
            'address_city' => ['required', 'string', 'min:3', 'max:255'],
            'address_state' => ['required', 'string', 'min:3', 'max:255'],
            'address_zipcode' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
