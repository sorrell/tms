<?php

namespace App\Http\Resources\Documents;

use App\Http\Resources\UserResource;
use App\Traits\Resources\HasDates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Documents\Document
 */
class DocumentResource extends JsonResource
{
    use HasDates;
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
            'created_at' => $this->asDate($this->created_at),
            'updated_at' => $this->asDate($this->updated_at),
            'deleted_at' => $this->when($this->deleted_at !== null, $this->asDate($this->deleted_at)),
            'metadata' => $this->metadata,
            'folder_name' => $this->folder_name,
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'uploaded_by' => $this->whenLoaded('uploadedBy', fn () => UserResource::make($this->uploadedBy)),
            'temporary_url' => $this->getTemporaryUrl(),
        ];
    }
}

