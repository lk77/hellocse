<?php

namespace App\Services\Profile;

use App\Data\Profile\ProfileData;
use App\Enums\Profile\ProfileStatus;
use App\Interfaces\Services\Profile\ProfileServiceInterface;
use App\Models\Profile\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webmozart\Assert\Assert;

class EloquentProfileService implements ProfileServiceInterface
{
    /**
     * Retrieve a profile
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Profile>
     */
    public function get(int $id): ProfileData
    {
        // We retrieve the profile
        $profile = Profile::query()->with('user')->findOrFail($id);

        // We return the profile data
        return ProfileData::from($profile);
    }

    /**
     * Retrieve all the profiles
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        // We retrieve the profiles
        $profiles = Profile::query()->with('user')->where('status', ProfileStatus::active)->get();

        // We return the profiles data
        return $profiles->map(function (Profile $profile) {
            return ProfileData::from($profile);
        });
    }

    /**
     * Create a profile
     *
     * @param ProfileData $data
     * @return ProfileData
     */
    public function create(ProfileData $data): ProfileData
    {
        // We should have an image
        Assert::isInstanceOf($data->image, UploadedFile::class);

        // We store the file
        $imageName = $data->image->store('profile/image');

        // We verify that we have a string (store didn't fail)
        Assert::stringNotEmpty($imageName);

        // We retrieve the file original name
        $imageOriginalName = $data->image->getClientOriginalName();

        // We create the profile
        $profile = Profile::query()->create([
            'firstname' => $data->firstname,
            'lastname' => $data->lastname,
            'image_original_name' => $imageOriginalName,
            'image_name' => $imageName,
            'status' => $data->status->name,
            'user_id' => $data->user->id,
        ]);


        // We load the user relation
        $profile->load('user');

        // We return the profile data
        return ProfileData::from($profile);
    }

    /**
     * Update a profile
     *
     * @param ProfileData $data
     * @return ProfileData
     */
    public function update(ProfileData $data): ProfileData
    {
        // We should have an id
        Assert::notNull($data->id);

        // We retrieve the profile
        $profile = Profile::query()->with('user')->findOrFail($data->id);

        // We retrieve the image attributes
        $imageOriginalName = $profile->image_original_name;
        $imageName = $profile->image_name;

        // If we have a new image
        if ($data->image) {
            // We should have an image
            Assert::isInstanceOf($data->image, UploadedFile::class);

            // We store the file
            $imageName = $data->image->store('profile/image');

            // We verify that we have a string (store didn't fail)
            Assert::stringNotEmpty($imageName);

            // We retrieve the file original name
            $imageOriginalName = $data->image->getClientOriginalName();

            // We delete the old image
            Storage::disk('local')->delete($profile->image_name);
        }

        // We update the profile
        $profile->update([
            'firstname' => $data->firstname,
            'lastname' => $data->lastname,
            'image_original_name' => $imageOriginalName,
            'image_name' => $imageName,
            'status' => $data->status->name,
        ]);

        // We return the profile data
        return ProfileData::from($profile);
    }

    /**
     * We delete the profile
     *
     * @param ProfileData $data
     * @return bool
     */
    public function delete(ProfileData $data): bool
    {
        // We should have an id
        Assert::notNull($data->id);

        // We retrieve the profile
        $profile = Profile::query()->findOrFail($data->id);

        // We delete the image
        Storage::disk('local')->delete($profile->image_name);

        // We delete the profile
        return $profile->delete();
    }
}
