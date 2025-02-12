<?php

namespace App\Models\Customers;

use App\Models\Facility;
use App\Traits\HasOrganization;
use App\Traits\Notable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Scout\Searchable;

class Customer extends Model
{
    use HasFactory, HasOrganization, Searchable, Notable;

    protected $fillable = [
        'organization_id',
        'name',
    ];

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s", $this->name);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Facility>
     */
    public function facilities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'customer_facilities');
    }
}
