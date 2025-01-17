<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use App\Models\Organizations\OrganizationUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (User $user) {
            if ($user->isDirty('current_organization_id')) {
                $isMember = $user->organizations()
                    ->where('organizations.id', $user->current_organization_id)
                    ->exists();

                if (!$isMember) {
                    throw new \Illuminate\Auth\Access\AuthorizationException(
                        'You can only set your current organization to one you are a member of.'
                    );
                }
            }
        });
    }

    public function currentOrganization()
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_users')
            ->using(OrganizationUser::class)
            ->withTimestamps();
    }

    public function organizationInvites()
    {
        return OrganizationInvite::where('email', strtolower($this->email));
    }
}
