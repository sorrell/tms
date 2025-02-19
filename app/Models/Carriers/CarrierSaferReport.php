<?php

namespace App\Models\Carriers;

use App\Models\Carrier;
use Illuminate\Database\Eloquent\Model;

class CarrierSaferReport extends Model
{
    protected $fillable = [
        'dot_number',
        'report',
    ];

    protected $casts = [
        'report' => 'array',
    ];

    public function carrier(): ?Carrier
    {
        return Carrier::where('dot_number', $this->dot_number)->first();
    }

    public static function createFromFmcsaReport(array $report): self
    {
        return self::create([
            'dot_number' => $report['general']['carrier']['dotNumber'],
            'report' => $report,
        ]);
    }
    
    public static function createFromFmcsaReports(array $reports): array
    {
        $carrierSaferReports = [];

        foreach ($reports as $report) {
            $carrierSaferReports[] = self::createFromFmcsaReport($report);
        }

        return $carrierSaferReports;
    }
}