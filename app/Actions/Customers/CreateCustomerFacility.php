<?php

namespace App\Actions\Customers;

use App\Http\Resources\FacilityResource;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerFacility;
use App\Models\Facility;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCustomerFacility
{
    use AsAction;

    public function handle(int $customer_id, int $facility_id): Facility
    {
        $customer = Customer::findOrFail($customer_id);
        $facility = Facility::findOrFail($facility_id);

        CustomerFacility::create([
            'customer_id' => $customer_id,
            'facility_id' => $facility_id,
        ]);

        return $facility;
    }

    public function asController(ActionRequest $request, Customer $customer) : Facility
    {
        return $this->handle(
            customer_id: $customer->id,
            facility_id: $request->validated('facility_id'),
        );
    }

    public function htmlResponse(Facility $facility)
    {
        return redirect()->back()->with('success', 'Facility attached to customer successfully');
    }

    public function jsonResponse(Facility $facility) : FacilityResource
    {
        return FacilityResource::make($facility);
    }

    public function rules() : array
    {
        return [
            'facility_id' => ['required', 'exists:facilities,id'],
        ];
    }
}
