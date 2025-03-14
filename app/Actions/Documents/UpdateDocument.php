<?php

namespace App\Actions\Documents;

use App\Http\Resources\Documents\DocumentResource;
use App\Models\Documents\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateDocument
{
    use AsAction;

    public function handle(
        Document $document,
        string $fileName,
        string $folderName
    )
    {

        $document->name = $fileName;
        $document->folder_name = $folderName;
        $document->save();

        return $document;
    }

    public function htmlResponse(Document $document)
    {
        return redirect()->back()->with('success', 'Document updated successfully');
    }

    public function jsonResponse(Document $document)
    {
        return response()->json(DocumentResource::make($document));
    }

    public function asController(ActionRequest $request, Document $document)
    {

        return $this->handle(
            document: $document,
            fileName: $request->file('file_name'),
            folderName: $request->input('folder_name'),
        );
    }

    public function rules()
    {
        return [
            'file_name' => 'nullable|string|max:255',
            'folder_name' => 'nullable|string|max:255',
        ];
    }
}