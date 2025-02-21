<?php

namespace App\Actions\Carriers;

use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use App\Models\Carriers\CarrierSaferReport;
use App\Models\Location;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCarrierFromSaferReport
{
    use AsAction;

    public function handle(
        CarrierSaferReport $carrierSaferReport
    ): Carrier
    {

        $location = Location::firstOrCreate([
            'address_line_1' => $carrierSaferReport->report['general']['carrier']['phyStreet'],
            //'address_line_2' => $carrierSaferReport->report['general']['carrier']['phyStreet'],
            'address_city' => $carrierSaferReport->report['general']['carrier']['phyCity'],
            'address_state' => $carrierSaferReport->report['general']['carrier']['phyState'],
            'address_zipcode' => $carrierSaferReport->report['general']['carrier']['phyZipcode'],
        ]);

        $carrier = Carrier::create([
            'name' => $carrierSaferReport->name,
            'dot_number' => $carrierSaferReport->dot_number,
            'physical_location_id' => $location->id,
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
