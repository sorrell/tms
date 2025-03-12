<?php

namespace App\Http\Resources;

use App\Enums\Documentable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Documents\Document
 */
class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'folder_name' => $this->folder_name,
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'uploaded_by' => $this->whenLoaded('uploadedBy', fn () => UserResource::make($this->uploadedBy)),
            'temporary_url' => $this->getTemporaryUrl(),
        ];
    }
}

