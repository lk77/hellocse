<?php

namespace App\Http\Resources\Api\Profile\Comment;

use App\Data\Profile\CommentData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CommentData $commentData */
        $commentData = $this->resource;

        return [
            'id'         => $commentData->id,
            'content'    => $commentData->content,
            'user_id'    => $commentData->user->id,
            'user'       => $commentData->user,
            'profile_id' => $commentData->profileData->id,
            'profile'    => $commentData->profileData,
        ];
    }

    /**
     * Return a comment collection
     */
    public static function collection($resource): CommentCollection
    {
        return new CommentCollection($resource);
    }
}
