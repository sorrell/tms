<?php

namespace App\Traits;

use App\Enums\Documents\Documentable;
use App\Models\Documents\Document;
use App\Models\Documents\DocumentFolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocuments
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Document, $this>
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function documentFolders() : MorphMany
    {
        return $this->morphMany(DocumentFolder::class, 'documentable');
    }

    // Get all the document folder names for this object
    // including any custom ones
    public function getAllDocumentFolders() : array
    {
        $defaultFolders = $this->getDefaultDocumentFolders();

        $customFolders = $this->documentFolders;

        return [
            ...$defaultFolders,
            ...$customFolders
        ];
    }

    protected function getDefaultDocumentFolders(): array
    {
        return Documentable::getDefaultFoldersByClassName(get_class($this));
    }
}
