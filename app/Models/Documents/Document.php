<?php

namespace App\Models\Documents;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'path',
        'folder_name',
        'documentable_id',
        'documentable_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function documentable() : MorphTo
    {
        return $this->morphTo();
    }
}
