<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationInvite extends Model
{
    use HasTimestamps, SoftDeletes, HasOrganization;

    protected $fillable = [
        'organization_id',
        'used_by_id',
        'email',
        'accepted_at',
        'expire_at',
        'code',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    public function scopeOpen($query)
    {
        return $query->whereNull('accepted_at')->where('expire_at', '>', now());
    }


}
