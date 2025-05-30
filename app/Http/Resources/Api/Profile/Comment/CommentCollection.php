<?php

namespace App\Http\Resources\Api\Profile\Comment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CommentResource $resource */
        $resource = $this->resource;

        return $resource->toArray($request);
    }
}
