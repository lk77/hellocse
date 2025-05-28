<?php

namespace App\Http\Resources\Api\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProfileCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ProfileResource $resource */
        $resource = $this->resource;

        return $resource->toArray($request);
    }
}
