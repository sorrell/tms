<?php

namespace App\Models\Organizations;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasTimestamps;

    protected $fillable = [
        'id',
        'name',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_users')
            ->using(OrganizationUser::class)
            ->withTimestamps();
    }

    public function invites()
    {
        return $this->hasMany(OrganizationInvite::class);
    }

    public function roles() : HasMany
    {
        return $this->hasMany(
                config('permission.models.role')
        );
    }
}
