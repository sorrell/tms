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
            /**
             * Ignoring phpstan here, unsure how to tell it to use the trait on the model
             * Tried a lot of things, unable to make it happy
             */
            // @phpstan-ignore-next-line
            'documents' => DocumentResource::collection($documentable->documents),
            // @phpstan-ignore-next-line
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

        /** @var \App\Enums\Documents\Documentable $documentableType */
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