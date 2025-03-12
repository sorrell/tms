<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Timezone extends Model
{
    // TODO - this model just has a few TZ identifier options
    // when we come back and add the option to select your TZ for all dates
    // to be local or a specific one, we can use this table as the setter for the 
    // user TZ entry
    use Searchable;
    protected $fillable = [
        'name',
    ];
}