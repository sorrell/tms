<?php

namespace App\Actions\Facilities;

use App\Models\Contact;
use App\Models\Documents\Document;
use App\Models\Facility;
use App\Traits\HandlesAuditHistory;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetFacilityAuditHistory
{
    use AsAction, HandlesAuditHistory;

    public function handle(Facility $facility)
    {
        $audits = $this->getAuditHistory($facility);
        return $this->formatAuditData($audits);
    }

    public function asController(ActionRequest $request, Facility $facility)
    {
        $audits = $this->handle($facility);
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
        return $request->user()->can(\App\Enums\Permission::FACILITY_VIEW);
    }

    protected function getModelSpecificFieldMappings(string $auditableType): array
    {
        // Handle facility-specific fields
        if ($auditableType === Facility::class) {
            return [
                'location_id' => 'Location',
                'organization_id' => 'Organization',
            ];
        }

        return [];
    }
}