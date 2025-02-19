<?php

namespace App\Http\Resources\Carriers;

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
            'report' => [
                'name' => $this->report['general']['carrier']['legalName'],
                'dba' => $this->report['general']['carrier']['dbaName'],
                'full_address' => sprintf('%s %s, %s %s', $this->report['general']['carrier']['phyStreet'], $this->report['general']['carrier']['phyCity'], $this->report['general']['carrier']['phyState'], $this->report['general']['carrier']['phyZipcode']),
                'address' => [
                    'street' => $this->report['general']['carrier']['phyStreet'],
                    'city' => $this->report['general']['carrier']['phyCity'],
                    'state' => $this->report['general']['carrier']['phyState'],
                    'zip' => $this->report['general']['carrier']['phyZipcode'],
                ],
                'raw' => $this->report,
            ],
            'is_full_report' => $this->report['full-report'] === 'true',
        ];
    }
}