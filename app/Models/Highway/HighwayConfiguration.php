<?php

namespace App\Models\Highway;

use App\Models\Organizations\Organization;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HighwayConfiguration extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'api_key',
        'environment',
        'auto_sync_enabled',
        'sync_frequency',
    ];

    protected $casts = [
        'auto_sync_enabled' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}