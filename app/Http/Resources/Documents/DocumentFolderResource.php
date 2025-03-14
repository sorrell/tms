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
            return [
                'id' => $this->id,
                'name' => $this->name,
            ];
        } 

        // If it's an enum value
        if ($this->resource instanceof \App\Enums\Documents\DocumentFolder) {
            return [
                'name' => $this->resource->value
            ];
        }

        // Fallback case
        return [
            'name' => $this->name
        ];
    }
}

