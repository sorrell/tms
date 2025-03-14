<?php

namespace App\Models\Documents;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class DocumentFolder extends Model
{
    use HasOrganization, SoftDeletes;

    protected $fillable = [
        'id',
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
