<?php

namespace App\Models\Shipments;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrailerSize extends Model
{
    use SoftDeletes, HasOrganization;

    protected $fillable = [
        'name',
        'organization_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Shipment, $this>
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
