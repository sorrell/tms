<?php

namespace App\Models\Carriers;

use App\Models\Carriers\Carrier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class CarrierSaferReport extends Model
{
    protected $fillable = [
        'dot_number',
        'report',
    ];

    protected $casts = [
        'report' => 'array',
    ];

    public function getNameAttribute(): string 
    {
        return $this->report['general']['carrier']['legalName'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Carrier, $this>
     */
    public function carrier() : BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'dot_number', 'dot_number');
    }

    public static function createFromFmcsaReport(array $report): self
    {
        return self::create([
            'dot_number' => $report['general']['carrier']['dotNumber'],
            'report' => $report,
        ])->load('carrier');
    }
    
    /**
     * @return \Illuminate\Support\Collection<int, self>
     */
    public static function createFromFmcsaReports(array $reports): Collection
    {
        return collect($reports)->map(fn ($report) => self::createFromFmcsaReport($report));
    }
}