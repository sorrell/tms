<?php

namespace App\Actions\Customers;

use App\Models\Contact;
use App\Models\Customers\Customer;
use App\Models\Documents\Document;
use App\Traits\HandlesAuditHistory;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCustomerAuditHistory
{
    use AsAction, HandlesAuditHistory;

    public function handle(Customer $customer)
    {
        $audits = $this->getAuditHistory($customer);
        return $this->formatAuditData($audits);
    }

    public function asController(ActionRequest $request, Customer $customer)
    {
        $audits = $this->handle($customer);
        return $this->jsonResponse($audits);
    }

    public function jsonResponse($audits)
    {
        return response()->json([
            'audits' => $audits,
        ]);
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::CUSTOMER_VIEW);
    }

    protected function getModelSpecificFieldMappings(string $auditableType): array
    {
        // Handle customer-specific fields
        if ($auditableType === Customer::class) {
            return [
                'net_pay_days' => 'Net Pay Days',
                'billing_location_id' => 'Billing Location',
                'dba_name' => 'DBA Name',
                'invoice_number_schema' => 'Invoice Number Schema',
                'billing_contact_id' => 'Billing Contact',
                'organization_id' => 'Organization',
            ];
        }

        return [];
    }
}