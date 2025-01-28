<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrailerType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
