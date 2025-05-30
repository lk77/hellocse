<?php

namespace App\Interfaces\Services\Profile;

use App\Data\Profile\CommentData;

interface CommentServiceInterface
{
    public function check(int $profileId, int $userId): bool;

    public function create(CommentData $data): CommentData;
}
