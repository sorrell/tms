<?php

namespace App\Models\CheckCalls;

use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use App\Models\User;
use App\Traits\HasNotes;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CheckCall extends Model
{
    use HasOrganization, HasNotes, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'carrier_id',
        'shipment_id',
        'stop_id',
        'created_by',
        'is_late',
        'eta',
        'reported_trailer_temp',
        'contact_name',
        'contact_method',
        'contact_method_detail',
        'details',
    ];

    protected $casts = [
        'eta' => 'datetime',
        'details' => 'array',
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_by = $model->created_by ?? Auth::id();
        });

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ShipmentStop, $this>
     */
    public function stop(): BelongsTo
    {
        return $this->belongsTo(ShipmentStop::class, 'stop_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Carrier, $this>
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 