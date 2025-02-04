<?php

namespace App\Actions\Notes;

use App\Enums\Notable;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNotes
{
    use AsAction;

    public function handle(
        string $notableType,
        int $notableId,
    ): Collection
    {
        return Note::where('notable_type', $notableType)
            ->where('notable_id', $notableId)
            ->get();
    }

    public function asController(ActionRequest $request, string $notableType, int $notableId): Collection
    {
        $notes = $this->handle(
            Notable::from($notableType)->getClassName(),
            $notableId,
        );

        return $notes;
    }

    public function jsonResponse(Collection $notes)
    {
        return NoteResource::collection($notes->load('user'));
    }

    public function htmlResponse(Collection $notes)
    {
        return redirect()->back();
    }
}
