<?php

namespace App\Models\Documents;

use App\Models\User;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class Document extends Model implements Auditable
{
    use HasOrganization, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'organization_id',
        'uploaded_by',
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

    public function getTemporaryUrl() 
    {
        return Storage::temporaryUrl(
            $this->path,
            now()->addMinutes(5),
            [
                'ResponseContentDisposition' => 'attachment; filename="' . $this->name . '"'
            ]
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function uploadedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function documentable() : MorphTo
    {
        return $this->morphTo();
    }
}
