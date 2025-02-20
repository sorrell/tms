<?php

namespace App\Rules;

use Propaganistas\LaravelPhone\Rules\Phone;

class ValidPhoneNumber extends Phone
{
    public function __construct()
    {
        $this->countries = explode(',', trim(config('phones.valid_countries')));
    }

    public function message()
    {
        return 'The :attribute field must be a valid phone number within a supported country (' . implode(', ', $this->countries) .')';
    }
} 