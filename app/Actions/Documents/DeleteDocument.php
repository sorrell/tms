<?php

namespace App\Actions\Documents;

use App\Http\Resources\Documents\DocumentResource;
use App\Models\Documents\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteDocument
{
    use AsAction;

    public function handle(
        Document $document
    )
    {
        $document->delete();
        return $document;
    }

    public function htmlResponse(Document $document)
    {
        return redirect()->back()->with('success', 'Document deleted');
    }

    public function jsonResponse(Document $document)
    {
        return response()->json(DocumentResource::make($document));
    }

    public function asController(ActionRequest $request, Document $document)
    {

        return $this->handle(
            $document
        );
    }

    public function authorize(ActionRequest $request, Document $document) 
    {
        return $request->user()->can('delete', $document);
    }

    public function rules()
    {
        return [];
    }
}