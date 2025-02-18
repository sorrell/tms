<?php

namespace App\Actions\Carriers;

use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCarrier
{
    use AsAction;

    public function handle(
        string $name,
    ): Carrier
    {
        return Carrier::create([
            'name' => $name,
        ]);
    }

    public function asController(ActionRequest $request): Carrier
    {
        return $this->handle(
            name: $request->validated('name'),
        );
    }

    public function jsonResponse(Carrier $carrier)
    {
        return CarrierResource::make($carrier);
    }

    public function htmlResponse(Carrier $carrier)
    {
        return redirect()->route('carriers.show', $carrier);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
