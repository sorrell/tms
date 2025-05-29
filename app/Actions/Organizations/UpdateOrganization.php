<?php

namespace App\Actions\Organizations;

use App\Models\Organizations\Organization;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateOrganization
{
    use AsAction;

    public function handle(
        Organization $organization,
        string $name,
        ?string $company_name = null,
        ?string $company_address = null,
        ?string $company_city = null,
        ?string $company_state = null,
        ?string $company_zip = null,
        ?string $company_phone = null,
        ?string $company_email = null,
        ?string $accounting_contact_email = null,
        ?string $accounting_contact_phone = null,
    ): Organization {
        $organization->update([
            'name' => $name,
            'company_name' => $company_name,
            'company_address' => $company_address,
            'company_city' => $company_city,
            'company_state' => $company_state,
            'company_zip' => $company_zip,
            'company_phone' => $company_phone,
            'company_email' => $company_email,
            'accounting_contact_email' => $accounting_contact_email,
            'accounting_contact_phone' => $accounting_contact_phone,
        ]);

        return $organization->fresh();
    }

    public function asController(ActionRequest $request, Organization $organization)
    {
        $validated = $request->validated();
        
        $this->handle(
            $organization,
            $validated['name'],
            $validated['company_name'] ?? null,
            $validated['company_address'] ?? null,
            $validated['company_city'] ?? null,
            $validated['company_state'] ?? null,
            $validated['company_zip'] ?? null,
            $validated['company_phone'] ?? null,
            $validated['company_email'] ?? null,
            $validated['accounting_contact_email'] ?? null,
            $validated['accounting_contact_phone'] ?? null,
        );

        return redirect()->back()->with('success', 'Organization settings updated successfully');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_city' => ['nullable', 'string', 'max:255'],
            'company_state' => ['nullable', 'string', 'max:2'],
            'company_zip' => ['nullable', 'string', 'max:10'],
            'company_phone' => ['nullable', 'string', 'max:20'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'accounting_contact_email' => ['nullable', 'email', 'max:255'],
            'accounting_contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $organization = $request->route('organization');
        return $request->user()->can('update', $organization);
    }
}
