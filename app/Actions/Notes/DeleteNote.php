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

class DeleteNote
{
    use AsAction;

    public function handle(
        Note $note,
    ): Note
    {
        $note->delete();
        return $note;
    }

    public function asController(ActionRequest $request, Note $note): Note
    {
        $note = $this->handle(
            $note,
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
        return [];
    }

    public function authorize(ActionRequest $request): bool
    {
        $note = $request->route('note');
        return $request->user()->can('delete', $note);
    }
}
