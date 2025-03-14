<?php

namespace App\Http\Resources\Documents;

use App\Http\Resources\UserResource;
use App\Traits\Resources\HasDates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Documents\DocumentFolder|\App\Enums\Documents\DocumentFolder
 */
class DocumentFolderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // If it's a model instance
        if ($this->resource instanceof \App\Models\Documents\DocumentFolder) {
            /** @var \App\Models\Documents\DocumentFolder $model */
            $model = $this->resource;
            return [
                'id' => $model->id,
                'name' => $model->name,
            ];
        } 

        // If it's an enum value
        if ($this->resource instanceof \App\Enums\Documents\DocumentFolder) {
            /** @var \App\Enums\Documents\DocumentFolder $enum */
            $enum = $this->resource;
            return [
                'name' => $enum->value
            ];
        }

        // Fallback case
        return [
            'name' => $this->name
        ];
    }
}

