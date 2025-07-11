<?php

namespace App\Actions\Carriers;

use App\Models\Carriers\Carrier;
use App\Models\Contact;
use App\Models\Documents\Document;
use App\Traits\HandlesAuditHistory;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCarrierAuditHistory
{
    use AsAction, HandlesAuditHistory;

    public function handle(Carrier $carrier)
    {
        $audits = $this->getAuditHistory($carrier);
        return $this->formatAuditData($audits);
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

    protected function getModelSpecificFieldMappings(string $auditableType): array
    {
        // Handle carrier-specific fields
        if ($auditableType === Carrier::class) {
            return [
                'mc_number' => 'MC Number',
                'dot_number' => 'DOT Number',
                'contact_email' => 'Contact Email',
                'contact_phone' => 'Contact Phone',
                'billing_email' => 'Billing Email',
                'billing_phone' => 'Billing Phone',
                'physical_location_id' => 'Physical Location',
                'billing_location_id' => 'Billing Location',
                'organization_id' => 'Organization',
            ];
        }

        return [];
    }
}