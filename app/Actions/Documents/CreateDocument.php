<?php

namespace App\Actions\Documents;

use App\Enums\Documentable;
use App\Http\Resources\DocumentResource;
use App\Models\Documents\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateDocument
{
    use AsAction;

    public function handle(
        string $documentableType,
        int $documentableId,
        string $fileName,
        ?string $folderName = null,
        UploadedFile $file,
    )
    {

        $documentable = Documentable::from($documentableType)->getClassName()::findOrFail($documentableId);

        $path = Storage::putFileAs(
            sprintf('documents/%s/%s', $documentableType, $documentable->id),
            $file,
            $fileName,
        );

        $document = Document::create([
            'name' => $fileName,
            'folder_name' => $folderName,
            'documentable_id' => $documentable->id,
            'documentable_type' => $documentable->getMorphClass(),
            'path' => $path,
            'uploaded_by' => Auth::user()?->id,
        ]);

        return $document;
    }

    public function htmlResponse(Document $document)
    {
        return redirect()->back()->with('success', 'Document created successfully');
    }

    public function jsonResponse(Document $document)
    {
        return response()->json(DocumentResource::make($document));
    }

    public function asController(ActionRequest $request, string $documentableType, int $documentableId)
    {

        return $this->handle(
            documentableType: $documentableType,
            documentableId: $documentableId,
            fileName: $request->file('file')->getClientOriginalName(),
            folderName: $request->input('folder_name'),
            file: $request->file('file'),
        );
    }

    public function rules()
    {
        return [
            'file' => 'required|file',
            'folder_name' => 'nullable|string|max:255',
        ];
    }
}