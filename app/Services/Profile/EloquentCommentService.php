<?php

namespace App\Services\Profile;

use App\Data\Profile\CommentData;
use App\Data\Profile\ProfileData;
use App\Interfaces\Services\Profile\CommentServiceInterface;
use App\Models\Profile\Comment;
use Webmozart\Assert\Assert;

class EloquentCommentService implements CommentServiceInterface
{
    /**
     * Check if we can create a comment
     */
    public function check(int $profileId, int $userId): bool
    {
        return Comment::query()->where([
            'profile_id' => $profileId,
            'user_id' => $userId,
        ])->count() === 0;
    }

    /**
     * Create a profile
     */
    public function create(CommentData $data): CommentData
    {
        // We should not have any comment for that user and profile
        Assert::true($this->check($data->profileData->id, $data->user->id), 'There is already a comment on this profile.');

        // We create a comment
        $comment = Comment::query()->create([
            'content' => $data->content,
            'profile_id' => $data->profileData->id,
            'user_id' => $data->user->id,
        ]);

        // We load the profile and user relations
        $comment->load(['profile', 'profile.user', 'user']);

        // We return the comment data
        return CommentData::from($comment, [
            'profileData' => ProfileData::from($comment->profile),
        ]);
    }
}
