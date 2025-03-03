<?php

namespace App\Actions\Shipments;

use App\Enums\TemperatureUnit;
use App\Http\Requests\Shipments\UpdateShipmentGeneralRequest;
use App\Http\Requests\Shipments\UpdateShipmentNumberRequest;
use App\Models\Shipments\Shipment;
use App\States\Shipments\Dispatched;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;

class DispatchShipment
{
    use AsAction;

    public function handle(
        Shipment $shipment,
    ): Shipment {

        $shipment->state->transitionTo(Dispatched::class);

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
        );

        return redirect()->back()->with('success', 'Shipment dispatched successfully');
    }

    public function rules(): array
    {
        return [
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
