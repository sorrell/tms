<?php

namespace App\Models\Shipments;

use App\Models\Accounting\CarrierRateType;
use App\Models\Accounting\Currency;
use App\Models\Accounting\CustomerRateType;
use App\Models\Carriers\Carrier;
use App\Models\Customers\Customer;
use App\Models\Shipments\Shipment;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentCarrierRate extends Model
{
    use HasOrganization, HasTimestamps;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'carrier_id',
        'rate',
        'quantity',
        'total',
        'carrier_rate_type_id',
        'currency_id',
    ];

    protected $casts = [];


    /**
     * Get the shipment associated with this accessorial type.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipment() : BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier() : BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier_rate_type() : BelongsTo
    {
        return $this->belongsTo(CarrierRateType::class);
    }

    
    public function currency() : BelongsTo 
    {
        return $this->belongsTo(Currency::class);
    }


}
