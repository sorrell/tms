<?php

namespace App\Models\Shipments;


use App\Models\Accounting\AccessorialType;
use App\Models\Accounting\Currency;
use App\Models\Accounting\CustomerRateType;
use App\Models\Carriers\Carrier;
use App\Models\Customers\Customer;
use App\Models\Shipments\Shipment;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentCustomerRate extends Model
{
    use HasOrganization, HasTimestamps;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'customer_id',
        'rate',
        'quantity',
        'total',
        'customer_rate_type_id',
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
     * Get the customer associated with this accessorial type.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer_rate_type() : BelongsTo
    {
        return $this->belongsTo(CustomerRateType::class);
    }

    
    public function currency() : BelongsTo 
    {
        return $this->belongsTo(Currency::class);
    }


}
