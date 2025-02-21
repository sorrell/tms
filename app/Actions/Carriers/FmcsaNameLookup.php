<?php

namespace App\Actions\Carriers;

use App\Http\Resources\Carriers\CarrierResource;
use App\Http\Resources\Carriers\CarrierSaferReportResource;
use App\Integrations\FmcsaService;
use App\Models\Carriers\Carrier;
use App\Models\Carriers\CarrierSaferReport;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FmcsaNameLookup
{
    use AsAction;

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Carriers\CarrierSaferReport>
     */
    public function handle(
        string $name,
    ): Collection {
        $fmcsaService = new FmcsaService();

        $results = $fmcsaService->searchCarrierName($name);

        if (isset($results['error'])) {
            return collect([]);
        }

        $carrierSaferReports = CarrierSaferReport::createFromFmcsaReports($results);

        return $carrierSaferReports;
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Carriers\CarrierSaferReport>
     */
    public function asController(ActionRequest $request): Collection
    {
        return $this->handle(
            name: $request->validated('name'),
        );
    }

    public function jsonResponse(Collection $carrierSaferReports)
    {
        return CarrierSaferReportResource::collection($carrierSaferReports);
    }

    public function htmlResponse(Collection $carrierSaferReports)
    {
        return response('404');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
