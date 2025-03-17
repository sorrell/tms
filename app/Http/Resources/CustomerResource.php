<?php

namespace App\Http\Resources;

use App\Http\Resources\Documents\DocumentFolderResource;
use App\Http\Resources\Documents\DocumentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Customers\Customer
 */
class CustomerResource extends JsonResource
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

            'documents' => $this->whenLoaded('documents', DocumentResource::collection($this->documents)),
            'document_folders' => $this->whenLoaded('documents', DocumentFolderResource::collection($this->getAllDocumentFolders()))
        ];
    }
}
