<?php

namespace App\Actions\Notes;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNote
{
    use AsAction;

    public function handle(
        string $note,
        string $notableType,
        int $notableId,
        ?int $userId = null,
    ): Note
    {
        return Note::create([
            'note' => $note,
            'user_id' => $userId ?? Auth::id(),
            'notable_id' => $notableId,
            'notable_type' => $notableType,
        ]);
        
    }

    public function asController(ActionRequest $request): NoteResource
    {
        $note = $this->handle(
            $request->validated('note'),
            $request->validated('notable_type'),
            $request->validated('notable_id'),
            $request->validated('user_id'),
        );

        return NoteResource::make($note);
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string'],
            'notable_type' => ['required', 'string'],
            'notable_id' => ['required', 'integer'],
            'user_id' => ['nullable', 'integer'],
        ];
    }
}
