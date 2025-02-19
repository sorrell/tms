<?php

namespace App\Actions\Carriers;

use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use App\Models\Carriers\CarrierSaferReport;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCarrierFromSaferReport
{
    use AsAction;

    public function handle(
        CarrierSaferReport $carrierSaferReport
    ): Carrier
    {
        $carrier = Carrier::create([
            'name' => $carrierSaferReport->name,
            'dot_number' => $carrierSaferReport->dot_number,
        ]);

        return $carrier;
    }

    public function asController(ActionRequest $request, CarrierSaferReport $carrierSaferReport): Carrier
    {
        return $this->handle(
            $carrierSaferReport
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
        ];
    }
}
