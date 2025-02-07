<?php

namespace App\Actions\Notes;

use App\Enums\Notable;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

    public function asController(ActionRequest $request, string $notableType, int $notableId): Note
    {
        $note = $this->handle(
            $request->validated('note'),
            Notable::from($notableType)->getClassName(),
            $notableId,
            $request->validated('user_id'),
        );

        return $note;
    }

    public function jsonResponse(Note $note)
    {
        return NoteResource::make($note);
    }

    public function htmlResponse(Note $note)
    {
        return redirect()->back()->with('success', 'Note added successfully');
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:5', 'max:512'],
            'user_id' => ['nullable', 'integer'],
        ];
    }
}
