<?php

namespace App\Http\Controllers\Api\Profile;

use App\Data\Profile\CommentData;
use App\Data\Profile\ProfileData;
use App\Enums\Profile\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\Comment\StoreCommentRequest;
use App\Http\Requests\Api\Profile\DestroyProfileRequest;
use App\Http\Requests\Api\Profile\IndexProfileRequest;
use App\Http\Requests\Api\Profile\StoreProfileRequest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\Profile\Comment\CommentResource;
use App\Http\Resources\Api\Profile\ProfileCollection;
use App\Http\Resources\Api\Profile\ProfileResource;
use App\Interfaces\Services\Profile\CommentServiceInterface;
use App\Interfaces\Services\Profile\ProfileServiceInterface;

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
        // We create the dto
        $commentData = CommentData::from($request->validated(), [
            'user' => auth('api')->user(),
            'profileData' => $this->profileService->get($request->validated('profile_id')),
        ]);

        // We create the comment
        $comment = $this->commentService->create($commentData);

        // We return the created comment
        return CommentResource::make($comment);
    }
}
