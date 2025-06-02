<?php

namespace Tests\Unit\Services\Profile;

use App\Data\Profile\ProfileData;
use App\Enums\Profile\ProfileStatus;
use App\Models\Profile\Profile;
use App\Services\Profile\EloquentProfileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class EloquentProfileServiceTest extends TestCase
{
    private EloquentProfileService $profileService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profileService = app(EloquentProfileService::class);
    }

    public function test_can_get_a_profile(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create();

        // We recover the profile data
        $profileData = $this->profileService->get($profile->id);

        // We check the attributes
        $this->assertSame($profile->id, $profileData->id);
        $this->assertSame($profile->firstname, $profileData->firstname);
        $this->assertSame($profile->lastname, $profileData->lastname);
        $this->assertSame($profile->image_original_name, $profileData->image_original_name);
        $this->assertSame($profile->image_name, $profileData->image_name);
        $this->assertSame($profile->status, $profileData->status->name);
        $this->assertSame($profile->user_id, $profileData->user->id);
    }

    public function test_can_get_all_profiles(): void
    {
        /* @var Profile[] $profiles We create some profiles */
        $profiles = Profile::factory(5)->create();

        // We recover the profile datas
        $profileDatas = $this->profileService->getAll();

        foreach ($profiles as $index => $profile) {
            // We recover profile data
            $profileData = $profileDatas[$index];

            // We check the attributes
            $this->assertSame($profile->id, $profileData->id);
            $this->assertSame($profile->firstname, $profileData->firstname);
            $this->assertSame($profile->lastname, $profileData->lastname);
            $this->assertSame($profile->image_original_name, $profileData->image_original_name);
            $this->assertSame($profile->image_name, $profileData->image_name);
            $this->assertSame($profile->status, $profileData->status->name);
            $this->assertSame($profile->user_id, $profileData->user->id);
        }

        // We check that we have the correct number of profiles
        $this->assertCount(5, $profileDatas);
    }

    public function test_can_store_a_profile(): void
    {
        /* @var Profile $profile We make a profile */
        $profile = Profile::factory()->make();

        $profileData = ProfileData::from([
            'firstname' => $profile->firstname,
            'lastname' => $profile->lastname,
            'image' => UploadedFile::fake()->createWithContent(
                $profile->image_original_name,
                File::get('/tmp/'.$profile->image_name)
            ),
            'user' => $profile->user,
            'status' => ProfileStatus::from($profile->status),
        ]);

        // We store the profile data
        $profileData = $this->profileService->create($profileData);

        // We should have an id
        $this->assertGreaterThan(0, $profileData->id);

        // We should have the profile in database
        $this->assertDatabaseHas('profiles', [
            'firstname' => $profile->firstname,
            'lastname' => $profile->lastname,
            'image_original_name' => $profile->image_original_name,
            'user_id' => $profile->user->id,
        ]);
    }

    public function test_can_update_a_profile(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create([
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $profileData = ProfileData::from([
            'id' => $profile->id,
            'firstname' => $profile->firstname,
            'lastname' => $profile->lastname,
            'image' => UploadedFile::fake()->createWithContent(
                $profile->image_original_name,
                File::get('/tmp/'.$profile->image_name)
            ),
            'user' => $profile->user,
            'status' => ProfileStatus::from($profile->status),
        ]);

        // We store the profile data
        $this->profileService->update($profileData);

        // We refresh the model
        $profile->refresh();

        // The model should have been updated
        $this->assertGreaterThan($profile->created_at, $profile->updated_at);
    }

    public function test_can_delete_a_profile(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create([
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $profileData = ProfileData::from([
            'id' => $profile->id,
            'firstname' => $profile->firstname,
            'lastname' => $profile->lastname,
            'image' => UploadedFile::fake()->createWithContent(
                $profile->image_original_name,
                File::get('/tmp/'.$profile->image_name)
            ),
            'user' => $profile->user,
            'status' => ProfileStatus::from($profile->status),
        ]);

        // We delete the profile data
        $this->profileService->delete($profileData);

        // The profile should be deleted
        $this->assertDatabaseMissing('profiles', [
            'id' => $profile->id,
        ]);
    }

}
