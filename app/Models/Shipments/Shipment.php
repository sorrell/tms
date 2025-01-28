<?php

namespace App\Models\Shipments;

use App\Models\Carrier;
use App\Models\Shipper;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Shipment extends Model
{
    use HasOrganization, Searchable;

    protected $fillable = [
        'organization_id',
        'carrier_id',
        'weight',
        'trip_distance',
        'trailer_type_id',
        'trailer_temperature_range',
        'trailer_temperature',
        'trailer_temperature_maximum',
    ];  

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("Shipment %s", $this->id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TrailerType, $this>
     */
    public function trailer_type(): BelongsTo
    {
        return $this->belongsTo(TrailerType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Carrier, $this>
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Shipper, $this>
     */
    public function shippers(): BelongsToMany
    {
        return $this->belongsToMany(Shipper::class, 'shipment_shippers')->using(ShipmentShipper::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentStop, $this>
     */
    public function stops(): HasMany
    {
        return $this->hasMany(ShipmentStop::class);
    }
}
