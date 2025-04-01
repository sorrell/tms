<?php

namespace App\Actions\Accounting;

use App\Http\Resources\Accounting\CustomerRateTypeResource;
use App\Models\Accounting\CustomerRateType;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCustomerRateTypes {
    use AsAction;

    public function handle() : Collection
    {
        return CustomerRateType::all();
    }

    public function asController()
    {
        return $this->handle();
    }

    public function jsonResponse(Collection $rateTypes)
    {
        return response()->json(CustomerRateTypeResource::collection($rateTypes));
    }

}