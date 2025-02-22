<?php

namespace App\Models\Carriers;

use App\Http\Resources\Carriers\CarrierResource;
use App\Models\Location;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Carrier extends Model
{
    use HasFactory, HasOrganization, Searchable;

    protected $fillable = [
        'organization_id',
        'name',
        'mc_number',
        'dot_number',
        'contact_email',
        'contact_phone',
        'physical_location_id',
        'billing_email',
        'billing_phone',
        'billing_location_id',
    ];

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s", $this->name);
    }

    /**
     * Defines the searchable content for scout search
     */
    public function toSearchableArray()
    {
        return new CarrierResource($this->load('physical_location', 'billing_location'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Location, $this>
     */
    public function physical_location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'physical_location_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Location, $this>
     */
    public function billing_location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'billing_location_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CarrierSaferReport, $this>
     */
    public function safer_reports() : HasMany
    {
        return $this->hasMany(CarrierSaferReport::class, 'dot_number', 'dot_number');
    }



}
