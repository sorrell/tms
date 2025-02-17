<?php

namespace App\Traits;

use App\Models\Note;
use App\Models\Organizations\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasNotes
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Note, $this>
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }

    /**
     * @param string $note
     * @param \App\Models\User|null $user
     * @return \App\Models\Note
     */
    public function addNote(string $note, ?User $user = null): Note
    {
        return $this->notes()->create([
            'note' => $note,
            'user_id' => $user ? $user->id : auth()->id(),
        ]);
    }
}
