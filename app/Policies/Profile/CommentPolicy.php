<?php

namespace App\Policies\Profile;

use App\Models\User\User;

class CommentPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('comment.create');
    }
}
