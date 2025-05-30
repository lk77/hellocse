<?php

namespace Api\Profile\Comment;

use App\Models\Profile\Comment;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StoreCommentTest extends TestCase
{
    public function test_can_store_comment_while_authenticated(): void
    {
        /* @var Comment $comment We make a comment */
        $comment = Comment::factory()->make();

        // We give the permission to create a comment
        $comment->user->givePermissionTo(Permission::query()->where(['name' => 'comment.create', 'guard_name' => 'api'])->firstOrFail());

        // We create a token
        $result = $comment->user->createToken('test');

        // We retrieve the json from the endpoint
        $json = $this->withToken($result->accessToken)->post(route('comment.store'), [
            'content' => $comment->content,
            'profile_id' => $comment->profile_id,
        ])->assertSuccessful()->json();

        // We recover the comment data
        $commentData = $json['data'];

        // We check the attributes
        $this->assertSame($comment->content, $commentData['content']);
        $this->assertSame($comment->user_id, $commentData['user_id']);
        $this->assertSame($comment->profile_id, $commentData['profile_id']);
    }

    public function test_cannot_store_two_comments_for_one_profile(): void
    {
        /* @var Comment $comment We make a comment */
        $comment = Comment::factory()->make();

        // We give the permission to create a comment
        $comment->user->givePermissionTo(Permission::query()->where(['name' => 'comment.create', 'guard_name' => 'api'])->firstOrFail());

        // We create a token
        $result = $comment->user->createToken('test');

        // We retrieve the json from the endpoint
        $json = $this->withToken($result->accessToken)->post(route('comment.store'), [
            'content' => $comment->content,
            'profile_id' => $comment->profile_id,
        ])->assertSuccessful()->json();

        // We recover the comment data
        $commentData = $json['data'];

        // We check the attributes
        $this->assertSame($comment->content, $commentData['content']);
        $this->assertSame($comment->user_id, $commentData['user_id']);
        $this->assertSame($comment->profile_id, $commentData['profile_id']);

        // We should have a validation error if we submit another comment
        $this->withToken($result->accessToken)->post(route('comment.store'), [
            'content' => $comment->content.' 2',
            'profile_id' => $comment->profile_id,
        ])->assertJsonValidationErrorFor('profile_id');
    }

    public function test_cannot_store_comment_while_unauthenticated(): void
    {
        /* @var Comment $comment We make a comment */
        $comment = Comment::factory()->make();

        // We retrieve the json from the endpoint
        $this->post(route('comment.store'), [
            'content' => $comment->content,
            'profile_id' => $comment->profile_id,
        ])->assertUnauthorized();
    }
}
