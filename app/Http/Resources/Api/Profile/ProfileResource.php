<?php

namespace App\Http\Resources\Api\Profile;

use App\Data\ProfileData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ProfileData $profileData */
        $profileData = $this->resource;

        return [
            'id' => $profileData->id,
            'firstname' => $profileData->firstname,
            'lastname' => $profileData->lastname,
            'image_original_name' => $profileData->image_original_name,
            'image_name' => $profileData->image_name,
            'status' => $this->when(auth('api')->check(), fn () => $profileData->status),
            'user_id' => $profileData->user->id,
            'user' => $profileData->user,
        ];
    }

    /**
     * Return a profile collection
     */
    public static function collection($resource): ProfileCollection
    {
        return new ProfileCollection($resource);
    }
}
