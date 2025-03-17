<?php

namespace App\Policies\Documents;

use App\Enums\Permission;
use App\Models\Documents\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        return $user->can('view', $document->documentable);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Model $documentable): bool
    {
        return $user->can('update', $documentable);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->can('update', $document->documentable);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->can('delete', $document->documentable);
    }

}
