<?php

namespace App\Actions\Documents;

use App\Enums\Documentable;
use App\Http\Resources\Documents\DocumentResource;
use App\Models\Documents\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDocument
{
    use AsAction;

    public function handle(
        Document $document
    )
    {
        return $document;
    }

    public function htmlResponse(Document $document)
    {
        return redirect($document->getTemporaryUrl());
    }

    public function jsonResponse(Document $document)
    {
        return response()->json(DocumentResource::make($document));
    }

    public function asController(ActionRequest $request, Document $document)
    {

        return $this->handle(
            document: $document,
        );
    }

    public function rules()
    {
        return [];
    }
}