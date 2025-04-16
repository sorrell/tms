<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $key
 * @property mixed $value
 * @property string $provider
 * @property bool $is_encrypted
 * @property bool $expose_to_frontend
 * @property string $label
 * @property string $description
 */
class GlobalIntegrationSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this['key'],
            'value' => $this['is_encrypted'] || !$this['expose_to_frontend'] ? null : $this['value'],
            'provider' => $this['provider'],
            'encrypted' => $this['is_encrypted'],
            'expose_to_frontend' => $this['expose_to_frontend'],
            'label' => $this['label'],
            'description' => $this['description'],
        ];
    }
}
