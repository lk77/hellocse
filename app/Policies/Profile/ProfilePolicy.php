<?php

namespace App\Policies\Profile;

use App\Models\Profile\Profile;
use App\Models\User\User;

class ProfilePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('profile.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Profile $profile): bool
    {
        return $user->can('profile.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Profile $profile): bool
    {
        return $user->can('profile.delete');
    }
}
