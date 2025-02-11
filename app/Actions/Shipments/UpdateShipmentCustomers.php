<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShipmentCustomers
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        array $customerIds,
    ): Shipment {

        $shipment->customers()->sync($customerIds);

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->customer_ids,
        );

        return redirect()->back()->with('success', 'Shipment customers updated successfully');
    }

    public function rules(): array
    {
        return [
            'customer_ids' => ['required', 'array'],
            'customer_ids.*' => ['required', 'exists:customers,id'],
        ];
    }
}
