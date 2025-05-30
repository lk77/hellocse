<?php

namespace App\Http\Controllers\Api\Profile;

use App\Data\Profile\ProfileData;
use App\Enums\Profile\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\DestroyProfileRequest;
use App\Http\Requests\Api\Profile\IndexProfileRequest;
use App\Http\Requests\Api\Profile\StoreProfileRequest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\Profile\ProfileCollection;
use App\Http\Resources\Api\Profile\ProfileResource;
use App\Interfaces\Services\Profile\ProfileServiceInterface;
use App\Models\Profile\Profile;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileServiceInterface $profileService,
    ) {}

    /**
     * Return all the active profiles
     */
    public function index(IndexProfileRequest $request): ProfileCollection
    {
        // We recover the profiles
        $profiles = $this->profileService->getAll();

        // We return the profiles
        return ProfileResource::collection($profiles);
    }

    /**
     * Create a profile
     *
     * @param StoreProfileRequest $request
     * @return ProfileResource
     */
    public function store(StoreProfileRequest $request): ProfileResource
    {
        // We create the dto
        $profileData = ProfileData::from($request->validated(), [
            'user' => auth('api')->user(),
            'status' => ProfileStatus::from($request->validated('status')),
        ]);

        // We create the profile
        $profile = $this->profileService->create($profileData);

        // We return the created profile
        return ProfileResource::make($profile);
    }

    /**
     * Update a profile
     *
     * @param UpdateProfileRequest $request
     * @param Profile $profile
     * @return ProfileResource
     */
    public function update(UpdateProfileRequest $request, Profile $profile): ProfileResource
    {
        // We create the dto
        $profileData = ProfileData::from($profile->toArray(), $request->validated(), [
            'user' => $profile->user,
            'status' => ProfileStatus::from($request->validated('status')),
        ]);

        // We update the profile
        $profile = $this->profileService->update($profileData);

        // We return the updated profile
        return ProfileResource::make($profile);
    }

    /**
     * Delete a profile
     *
     * @param DestroyProfileRequest $request
     * @param Profile $profile
     * @return JsonResponse
     */
    public function destroy(DestroyProfileRequest $request, Profile $profile): JsonResponse
    {
        // We create the dto
        $profileData = ProfileData::from($profile->toArray(), [
            'user' => $profile->user,
            'status' => ProfileStatus::from($profile->status),
        ]);

        // We delete the profile
        $deleted = $this->profileService->delete($profileData);

        // We return the status of the deletion
        return response()->json([
            'message' => __($deleted ? 'The profile was successfully deleted.' : 'The profile was not deleted successfully.'),
        ], $deleted ? 200 : 500);
    }
}
