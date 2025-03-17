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
        ?string $fileName,
        ?string $folderName
    )
    {
        if ($fileName !== null) {
            $document->name = $fileName;
        }

        if ($folderName !== null) {
            $document->folder_name = $folderName;
        }
        
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
        $inputs = $request->all();
        $fileName = key_exists('file_name', $inputs) ? strval($inputs['file_name']) : null;
        $folderName = key_exists('folder_name', $inputs) ? strval($inputs['folder_name']) : null;

        return $this->handle(
            document: $document,
            fileName: $fileName,
            folderName: $folderName
        );
    }

    public function authorize(ActionRequest $request) 
    {
        $document = $request->route('document');
        return $request->user()->can('update', $document);
    }

    public function rules()
    {
        return [
            'file_name' => 'nullable|string|max:255',
            'folder_name' => 'nullable|string|max:255',
        ];
    }
}