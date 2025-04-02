<?php

namespace App\Actions\Accounting;

use App\Http\Resources\Accounting\AccessorialTypeResource;
use App\Models\Accounting\AccessorialType;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAccessorialTypes {
    use AsAction;

    public function handle() : Collection
    {
        return AccessorialType::all();
    }

    public function asController()
    {
        return $this->handle();
    }

    public function jsonResponse(Collection $rateTypes)
    {
        return response()->json(AccessorialTypeResource::collection($rateTypes));
    }
}