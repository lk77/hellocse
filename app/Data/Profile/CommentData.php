<?php

namespace App\Data\Profile;

use App\Models\User\User;
use Spatie\LaravelData\Data;

class CommentData extends Data
{
    public function __construct(
        public ?int $id,
        public string $content,
        public ProfileData $profileData,
        public User $user
    ) {}
}
