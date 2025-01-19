<?php

namespace App\Models\Organizations;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationUser extends Pivot
{
    use HasTimestamps;

    public $incrementing = true;
    public $table = 'organization_users';

    protected $fillable = [
        'id',
        'organization_id',
        'user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Organization, $this>
     */
    public function organization() : BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
