<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Timezone extends Model
{
    use Searchable;
    protected $fillable = [
        'name',
        'offset',
    ];
}