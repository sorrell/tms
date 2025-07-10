<?php

namespace App\Actions\Customers;

use App\Models\Customers\Customer;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use OwenIt\Auditing\Models\Audit;

class GetCustomerAuditHistory
{
    use AsAction;

    public function handle(Customer $customer)
    {
        return $customer->audits()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Audit $audit) {
                return [
                    'id' => $audit->id,
                    'event' => $audit->event,
                    'user' => $audit->user ? [
                        'id' => $audit->user->id,
                        'name' => $audit->user->name,
                        'email' => $audit->user->email,
                    ] : null,
                    'old_values' => $audit->old_values ?? [],
                    'new_values' => $audit->new_values ?? [],
                    'url' => $audit->url,
                    'ip_address' => $audit->ip_address,
                    'user_agent' => $audit->user_agent,
                    'created_at' => $audit->created_at,
                    'created_at_human' => $audit->created_at->diffForHumans(),
                    'changes' => $this->formatChanges($audit->old_values ?? [], $audit->new_values ?? []),
                ];
            });
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

    private function formatChanges(array $oldValues, array $newValues): array
    {
        $changes = [];
        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[] = [
                    'field' => $this->formatFieldName($key),
                    'field_name' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function formatFieldName(string $field): string
    {
        return match ($field) {
            'net_pay_days' => 'Net Pay Days',
            'billing_location_id' => 'Billing Location',
            'dba_name' => 'DBA Name',
            'invoice_number_schema' => 'Invoice Number Schema',
            'billing_contact_id' => 'Billing Contact',
            'organization_id' => 'Organization',
            default => ucwords(str_replace('_', ' ', $field)),
        };
    }
}