<?php

namespace App\Actions\Customers;

use App\Http\Resources\FacilityResource;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerFacility;
use App\Models\Facility;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCustomerFacility
{
    use AsAction;

    public function handle(int $customerId, int $facilityId): Facility
    {
        $customerFacility = CustomerFacility::where('customer_id', $customerId)->where('facility_id', $facilityId)->firstOrFail();
        $customerFacility->delete();

        return $customerFacility->facility;
    }

    public function asController(ActionRequest $request, Customer $customer, Facility $facility) : Facility
    {
        return $this->handle(
            customerId: $customer->id,
            facilityId: $facility->id,
        );
    }

    public function htmlResponse(Facility $facility)
    {
        return redirect()->back()->with('success', 'Facility detached from customer successfully');
    }

    public function jsonResponse(Facility $facility) : FacilityResource
    {
        return FacilityResource::make($facility);
    }

    public function rules() : array
    {
        return [];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::CUSTOMER_EDIT);
    }
}
