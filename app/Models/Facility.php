<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use HasFactory, HasOrganization;

    protected $fillable = [
        'organization_id',
        'location_id',
        'name',
    ];

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s - %s, %s, %s", $this->name, $this->location->name, $this->location->address_city, $this->location->address_state);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
