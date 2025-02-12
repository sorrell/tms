<?php

namespace App\Models\Customers;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFacility extends Model
{

    protected $fillable = [
        'customer_id',
        'facility_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
