<?php

namespace App\Actions\Documents;

use App\Enums\Documents\Documentable;
use App\Http\Resources\Documents\DocumentFolderResource;
use App\Http\Resources\Documents\DocumentResource;
use App\Models\Documents\Document;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDocumentsWithFolders
{
    use AsAction;

    public function handle(
        Documentable $documentableType,
        string $documentableId
    ) : array
    {

        $documentable = $documentableType->modelFind($documentableId);

        return [
            'documents' => DocumentResource::collection($documentable->documents),
            'folders' => DocumentFolderResource::collection($documentable->getAllDocumentFolders())
        ];
    }

    public function htmlResponse(array $response)
    {
        // none
        return redirect()->back();
    }

    public function jsonResponse(array $response)
    {
        return $response;
    }

    public function asController(ActionRequest $request, Documentable $documentableType, string $documentableId)
    {

        return $this->handle(
            documentableType: $documentableType,
            documentableId: $documentableId
        );
    }

    public function authorize(ActionRequest $request) 
    {
        $documentableType = $request->route('documentableType');
        $documentableId = $request->route('documentableId');

        $documentable = $documentableType->modelFind($documentableId);
        return $request->user()->can('view', [Document::class, $documentable]);
    }

    public function rules()
    {
        return [];
    }
}