<?php

namespace App\Actions\Accounting;

use App\Http\Resources\Accounting\CarrierRateTypeResource;
use App\Models\Accounting\CarrierRateType;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCarrierRateTypes {
    use AsAction;

    public function handle() : Collection
    {
        return CarrierRateType::all();
    }

    public function asController()
    {
        return $this->handle();
    }

    public function jsonResponse(Collection $rateTypes)
    {
        return response()->json(CarrierRateTypeResource::collection($rateTypes));
    }

}