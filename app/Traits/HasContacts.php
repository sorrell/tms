<?php

namespace App\Traits;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContacts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Contact, $this>
     */
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contact_for');
    }
}
