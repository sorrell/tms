<?php

namespace App\Models\Highway;

use App\Models\Carriers\Carrier;
use App\Models\Organizations\Organization;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HighwayCarrierSyncLog extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'carrier_id',
        'sync_type',
        'highway_carrier_id',
        'status',
        'request_data',
        'response_data',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'synced_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('sync_type', $type);
    }
}