<?php

namespace App\Http\Controllers\Api\Profile;

use App\Data\Profile\CommentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\Comment\StoreCommentRequest;
use App\Http\Resources\Api\Profile\Comment\CommentResource;
use App\Interfaces\Services\Profile\CommentServiceInterface;
use App\Interfaces\Services\Profile\ProfileServiceInterface;
use Webmozart\Assert\Assert;

class CommentController extends Controller
{
    public function __construct(
        private readonly ProfileServiceInterface $profileService,
        private readonly CommentServiceInterface $commentService
    ) {}

    /**
     * Create a comment for a profile
     */
    public function store(StoreCommentRequest $request): CommentResource
    {
        // We recover the profile id
        $profileId = $request->validated('profile_id');
        Assert::integer($profileId);

        // We create the dto
        $commentData = CommentData::from($request->validated(), [
            'user'        => auth('api')->user(),
            'profileData' => $this->profileService->get($profileId),
        ]);

        // We create the comment
        $comment = $this->commentService->create($commentData);

        // We return the created comment
        return CommentResource::make($comment);
    }
}
