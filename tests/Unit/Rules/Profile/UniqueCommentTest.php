<?php

namespace Tests\Unit\Rules\Profile;

use App\Interfaces\Services\Profile\CommentServiceInterface;
use App\Models\Profile\Comment;
use App\Rules\Profile\UniqueComment;
use Tests\TestCase;

class UniqueCommentTest extends TestCase
{
    public function test_fail_when_comment_already_exists(): void
    {
        /* @var Comment $comment We make a comment */
        $comment = Comment::factory()->make();

        /* @var CommentServiceInterface $commentService */
        $commentService = $this->mock(CommentServiceInterface::class, function ($mock) {
            $mock->shouldReceive('check')->andReturnFalse();
        });

        $rule = new UniqueComment($commentService);

        $this->actingAs($comment->user, 'api');

        // We check the validation error
        $rule->validate('profile_id', $comment->profile_id, function (string $message) {
            $this->assertSame('There is already a comment on this profile.', $message);
        });
    }

    public function test_pass_when_comment_does_not_exists(): void
    {
        /* @var Comment $comment We make a comment */
        $comment = Comment::factory()->make();

        /* @var CommentServiceInterface $commentService */
        $commentService = $this->mock(CommentServiceInterface::class, function ($mock) {
            $mock->shouldReceive('check')->andReturnTrue();
        });

        $rule = new UniqueComment($commentService);

        $this->actingAs($comment->user, 'api');

        // We check the validation error
        $failed = false;
        $rule->validate('profile_id', $comment->profile_id, function () use (&$failed) {
            $failed = true;
        });

        // We shoud not have failed
        $this->assertFalse($failed);
    }
}
