<?php

namespace Tests\Unit\Rules\Profile;

use App\Interfaces\Services\Profile\CommentServiceInterface;
use App\Models\Profile\Comment;
use App\Rules\Profile\UniqueComment;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webmozart\Assert\Assert;

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

        Assert::implementsInterface($comment->user, OAuthenticatable::class);
        Passport::actingAs($comment->user);

        // We test the rule with the validator
        $validator = Validator::make(
            [
                'profile_id' => $comment->profile_id,
            ],
            [
                'profile_id' => [new UniqueComment($commentService)],
            ]
        );

        // We shoud have failed
        $this->assertFalse($validator->passes());

        // We check the error message
        $this->assertSame('There is already a comment on this profile.', $validator->getMessageBag()->first());
    }

    public function test_pass_when_comment_does_not_exists(): void
    {
        /* @var Comment $comment We make a comment */
        $comment = Comment::factory()->make();

        /* @var CommentServiceInterface $commentService */
        $commentService = $this->mock(CommentServiceInterface::class, function ($mock) {
            $mock->shouldReceive('check')->andReturnTrue();
        });

        Assert::implementsInterface($comment->user, OAuthenticatable::class);
        Passport::actingAs($comment->user);

        // We test the rule with the validator
        $validator = Validator::make(
            [
                'profile_id' => $comment->profile_id,
            ],
            [
                'profile_id' => [new UniqueComment($commentService)],
            ]
        );

        // We shoud not have failed
        $this->assertTrue($validator->passes());
    }
}
