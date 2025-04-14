<?php

namespace App\Models\Organizations;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class IntegrationSetting extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'key',
        'value',
        'provider',
        'expose_to_frontend',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'expose_to_frontend' => 'boolean',
    ];

    /**
     * Get the decrypted value
     */
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            return Crypt::decryptString($value);
        }
        
        return $value;
    }

    /**
     * Set the encrypted value when needed
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get the organization that owns this setting
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
} 