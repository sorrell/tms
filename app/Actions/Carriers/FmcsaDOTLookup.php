<?php

namespace App\Actions\Carriers;

use App\Http\Resources\CarrierResource;
use App\Http\Resources\Carriers\CarrierSaferReportResource;
use App\Integrations\FmcsaService;
use App\Models\Carrier;
use App\Models\Carriers\CarrierSaferReport;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FmcsaDOTLookup
{
    use AsAction;

    public function handle(
        string $dotNumber,
    ): CarrierSaferReport
    {
        $fmcsaService = new FmcsaService();

        $results = $fmcsaService->getFullReport($dotNumber);

        if (isset($results['error'])) {
            return [];
        }

        // We get a single result here so call single report not reports
        $carrierSaferReport = CarrierSaferReport::createFromFmcsaReport($results);

        return $carrierSaferReport;
    }

    public function asController(ActionRequest $request): CarrierSaferReport
    {
        return $this->handle(
            dotNumber: $request->validated('dotNumber'),
        );
    }

    public function jsonResponse(CarrierSaferReport $carrierSaferReport)
    {
        return new CarrierSaferReportResource($carrierSaferReport);
    }

    public function htmlResponse(CarrierSaferReport $carrierSaferReport)
    {
        return response('404');
    }

    public function rules(): array
    {
        return [
            'dotNumber' => ['required', 'string'],
        ];
    }
}
