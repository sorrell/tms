<?php

namespace App\Models\Shipments;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrailerType extends Model
{
    use SoftDeletes, HasFactory, HasOrganization;

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
