<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;

class Contact extends Model
{
    use HasOrganization, Searchable;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'mobile_phone',
        'office_phone',
        'office_phone_extension',
        'contact_for_id',
        'contact_for_type',
    ];

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf('%s', $this->name);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function contactFor() : MorphTo
    {
        return $this->morphTo();
    }
}
