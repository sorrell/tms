<?php

namespace App\Actions\Carriers;

use App\Models\Carriers\Carrier;
use App\Models\Carriers\CarrierSaferReport;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RefreshCarrierSaferReport
{
    use AsAction;

    public function handle(Carrier $carrier): ?CarrierSaferReport
    {
        // Check if carrier has a DOT number
        if (!$carrier->dot_number) {
            throw new \Exception('Carrier does not have a DOT number');
        }

        // Check if 24 hours have passed since last update
        if ($carrier->safer_report) {
            $lastUpdated = new \DateTime($carrier->safer_report->updated_at);
            $now = new \DateTime();
            $hoursSinceUpdate = ($now->getTimestamp() - $lastUpdated->getTimestamp()) / 3600;
            
            if ($hoursSinceUpdate < 24) {
                throw new \Exception('SAFER data can only be refreshed once every 24 hours');
            }
        }

        // Fetch new SAFER data
        $newReport = app(FmcsaDOTLookup::class)->handle($carrier->dot_number);
        
        if ($newReport) {
            // Touch the updated_at timestamp
            $newReport->touch();
        }

        return $newReport;
    }

    public function asController(Carrier $carrier, ActionRequest $request)
    {
        Gate::authorize(\App\Enums\Permission::CARRIER_EDIT);
        
        $this->handle($carrier);

        return redirect()->back()->with('success', 'SAFER data refreshed successfully');
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::CARRIER_EDIT->value);
    }
}