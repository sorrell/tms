<?php

namespace App\Actions\Carriers;

use App\Models\Carriers\Carrier;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use OwenIt\Auditing\Models\Audit;

class GetCarrierAuditHistory
{
    use AsAction;

    public function handle(Carrier $carrier)
    {
        return $carrier->audits()
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

    public function asController(ActionRequest $request, Carrier $carrier)
    {
        $audits = $this->handle($carrier);
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
        return $request->user()->can(\App\Enums\Permission::CARRIER_VIEW);
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
            'mc_number' => 'MC Number',
            'dot_number' => 'DOT Number',
            'contact_email' => 'Contact Email',
            'contact_phone' => 'Contact Phone',
            'billing_email' => 'Billing Email',
            'billing_phone' => 'Billing Phone',
            'physical_location_id' => 'Physical Location',
            'billing_location_id' => 'Billing Location',
            'organization_id' => 'Organization',
            default => ucwords(str_replace('_', ' ', $field)),
        };
    }
}