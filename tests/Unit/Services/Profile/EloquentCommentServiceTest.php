<?php

namespace Services\Profile;

use App\Data\Profile\CommentData;
use App\Data\Profile\ProfileData;
use App\Models\Profile\Comment;
use App\Models\Profile\Profile;
use App\Services\Profile\EloquentCommentService;
use Tests\TestCase;
use Webmozart\Assert\Assert;

class EloquentCommentServiceTest extends TestCase
{
    private EloquentCommentService $commentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commentService = app(EloquentCommentService::class);
    }

    public function test_can_check_that_comment_can_be_created(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create();

        Assert::integer($profile->user_id);

        // We should be allowed to create a comment
        $this->assertTrue($this->commentService->check($profile->id, $profile->user_id));
    }

    public function test_can_check_that_comment_cannot_be_created(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create();

        Assert::integer($profile->user_id);

        /* @var Comment $comment We create a comment */
        Comment::factory()->create([
            'profile_id' => $profile->id,
            'user_id'    => $profile->user_id,
        ]);

        // We should not be allowed to create another comment
        $this->assertFalse($this->commentService->check($profile->id, $profile->user_id));
    }

    public function test_can_store_a_comment(): void
    {
        /* @var Comment $comment We make a comment */
        $comment = Comment::factory()->make();

        $commentData = CommentData::from([
            'content'     => $comment->content,
            'profileData' => ProfileData::from($comment->profile, [
                'user' => $comment->user,
            ]),
            'user' => $comment->user,
        ]);

        // We store the comment data
        $commentData = $this->commentService->create($commentData);

        // We should have an id
        $this->assertGreaterThan(0, $commentData->id);

        // We should have the comment in database
        $this->assertDatabaseHas('comments', [
            'content'    => $comment->content,
            'profile_id' => $comment->profile_id,
            'user_id'    => $comment->user?->id,
        ]);
    }
}
