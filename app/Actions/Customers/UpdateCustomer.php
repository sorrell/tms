<?php

namespace App\Actions\Customers;

use App\Http\Resources\CustomerResource;
use App\Models\Customers\Customer;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCustomer
{
    use AsAction;

    public function handle(
        Customer $customer,
        ?string $name = null
    ): Customer
    {
        $customer->update(array_filter([
            'name' => $name,
        ], fn($value) => !is_null($value)));

        return $customer;
    }

    public function asController(ActionRequest $request, Customer $customer): Customer
    {
        return $this->handle(
            customer: $customer,
            name: $request->validated('name'),
        );
    }

    public function jsonResponse(Customer $customer)
    {
        return CustomerResource::make($customer);
    }

    public function htmlResponse(Customer $customer)
    {
        return redirect()->back()->with('success', 'Customer updated successfully');
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::CUSTOMER_EDIT);
    }
}
