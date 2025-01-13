<?php

namespace App\Models\Organizations;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationInvite extends Model
{
    use HasTimestamps, SoftDeletes, HasOrganization;

    protected $fillable = [
        'organization_id',
        'accepted_by_id',
        'email',
        'accepted_at',
        'expire_at',
        'code',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('open', function ($query) {
            $query->whereNull('accepted_at')->where('expire_at', '>', now());
        });
    }

    public function inviteUrl()
    {
        return route('organizations.invites.show', [$this->organization, $this]);
    }


}
