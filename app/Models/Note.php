<?php

namespace App\Models;

use App\Models\Organizations\Organization;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Note extends Model
{
    use SoftDeletes, HasOrganization;
    protected $fillable = [
        'organization_id',
        'notable_id',
        'notable_type',
        'note',
        'user_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = $model->user_id ?? Auth::id();
        });

        static::updating(function ($model) {
            $model->user_id = $model->user_id ?? Auth::id();
        });

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('created_at', 'desc');
        });

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

}
