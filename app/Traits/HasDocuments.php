<?php

namespace App\Traits;

use App\Models\Documents\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocuments
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Document, $this>
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
