<?php

namespace App\Models\Documents;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentFolder extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'documentable_id',
        'documentable_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function documentable() : MorphTo
    {
        return $this->morphTo();
    }
}
