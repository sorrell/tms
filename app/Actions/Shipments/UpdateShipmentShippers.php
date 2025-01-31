<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShipmentShippers
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        array $shipperIds,
    ): Shipment {

        $shipment->shippers()->sync($shipperIds);

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->shipper_ids,
        );

        return redirect()->back()->with('success', 'Shipment shippers updated successfully');
    }

    public function rules(): array
    {
        return [
            'shipper_ids' => ['required', 'array'],
            'shipper_ids.*' => ['required', 'exists:shippers,id'],
        ];
    }
}
