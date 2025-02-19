<?php

namespace App\Http\Resources\Carriers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarrierSaferReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dot_number' => $this->dot_number,
            'report' => $this->report,
            'full_report' => $this->report['full-report'] === 'true',
        ];
    }
}