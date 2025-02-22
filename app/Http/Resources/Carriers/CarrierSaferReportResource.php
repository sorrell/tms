<?php

namespace App\Http\Resources\Carriers;

use App\Http\Resources\Carriers\CarrierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Carriers\CarrierSaferReport
 */
class CarrierSaferReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dot_number' => $this->dot_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'report' => $this->report,
            'is_full_report' => $this->report['full-report'] === 'true',
            'carrier' => $this->whenLoaded('carrier', function () {
                return new CarrierResource($this->carrier);
            }),
        ];
    }
}