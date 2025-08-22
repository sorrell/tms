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
        $previousCustomerIds = $shipment->customers()->pluck('customers.id')->toArray();

        $shipment->customers()->sync($customerIds);

        $addedCustomers = array_diff($customerIds, $previousCustomerIds);
        $removedCustomers = array_diff($previousCustomerIds, $customerIds);

        // Touch the model to trigger updated event if customers changed
        if (!empty($addedCustomers) || !empty($removedCustomers)) {
            $shipment->touch();
        }


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

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
