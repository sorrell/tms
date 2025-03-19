<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{

    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'code',
        'symbol'
    ];
}
