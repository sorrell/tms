<?php

namespace App\Actions\Contacts;

use App\Enums\Contactable;
use App\Enums\ContactType;
use Lorisleiva\Actions\Concerns\AsAction;

class GetContactTypes
{
    use AsAction;

    public function handle(
        Contactable $contactable
    ): array
    {
        return $contactable->getContactTypes();
    }

    public function asController(Contactable $contactable): array
    {
        return $this->handle($contactable);
    }

    public function jsonResponse(array $contactTypes)
    {
        return response()->json($contactTypes);
    }
}