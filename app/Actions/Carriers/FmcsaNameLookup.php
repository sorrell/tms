<?php

namespace App\Actions\Carriers;

use App\Http\Resources\CarrierResource;
use App\Http\Resources\Carriers\CarrierSaferReportResource;
use App\Integrations\FmcsaService;
use App\Models\Carrier;
use App\Models\Carriers\CarrierSaferReport;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FmcsaNameLookup
{
    use AsAction;

    public function handle(
        string $name,
    ): array
    {
        $fmcsaService = new FmcsaService();

        $results = $fmcsaService->searchCarrierName($name);

        $carrierSaferReports = CarrierSaferReport::createFromFmcsaReports($results);

        return $carrierSaferReports;
    }

    public function asController(ActionRequest $request): array
    {
        return $this->handle(
            name: $request->validated('name'),
        );
    }

    public function jsonResponse(array $carrierSaferReports)
    {
        return CarrierSaferReportResource::collection($carrierSaferReports);
    }

    public function htmlResponse(array $carrierSaferReports)
    {
        return response(404);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
