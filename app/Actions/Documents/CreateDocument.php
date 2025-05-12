<?php

namespace App\Actions\Documents;

use App\Enums\Documents\Documentable;
use App\Http\Resources\Documents\DocumentResource;
use App\Models\Documents\Document;
use Exception;
use Illuminate\Http\File;
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
        File|UploadedFile $file,
        ?string $folderName = null,
    )
    {

        $documentable = Documentable::from($documentableType)->getClassName()::findOrFail($documentableId);

        // Check if a file with the same name already exists in the same path
        $basePath = sprintf('documents/%s/%s', $documentableType, $documentable->id);
        $originalFileName = $fileName;
        $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        $counter = 1;
        while (Storage::exists("{$basePath}/{$fileName}")) {
            $fileName = "{$fileNameWithoutExtension}-{$counter}.{$extension}";
            $counter++;
            if ($counter > 10) {
                throw new Exception("File name is matched 10+ times, please rename the file to be unique".$originalFileName);
            }
        }

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

    public function asController(ActionRequest $request)
    {

        return $this->handle(
            documentableType: $request->input('documentable_type'),
            documentableId: $request->input('documentable_id'),
            fileName: $request->file('file')->getClientOriginalName(),
            folderName: $request->input('folder_name'),
            file: $request->file('file'),
        );
    }

    public function authorize(ActionRequest $request) 
    {
        $documentableType = $request->input('documentable_type');
        $documentableId = $request->input('documentable_id');

        $documentable = Documentable::from($documentableType)
            ->getClassName()
            ::findOrFail($documentableId);

        return $request->user()->can('create', [Document::class, $documentable]);
    }

    public function rules()
    {
        return [
            'file' => 'required|file',
            'folder_name' => 'nullable|string|max:255',
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|string'
        ];
    }
}