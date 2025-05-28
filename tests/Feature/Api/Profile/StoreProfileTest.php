<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Profile\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StoreProfileTest extends TestCase
{
    public function test_can_store_profile_while_authenticated(): void
    {
        /* @var Profile $profile We make a profile */
        $profile = Profile::factory()->make();

        // We give the permission to create a profile
        $profile->user->givePermissionTo(Permission::query()->where(['name' => 'profile.create', 'guard_name' => 'api'])->firstOrFail());

        // We create a token
        $result = $profile->user->createToken('test');

        // We retrieve the json from the endpoint
        $json = $this->withToken($result->accessToken)->post(route('profile.store'), [
            'firstname' => $profile->firstname,
            'lastname' => $profile->lastname,
            'image' => UploadedFile::fake()->createWithContent(
                $profile->image_original_name,
                File::get('/tmp/'.$profile->image_name)
            ),
            'status' => $profile->status,
        ])->assertSuccessful()->json();

        // We recover the profile data
        $profileData = $json['data'];

        // We check the attributes
        $this->assertSame($profile->firstname, $profileData['firstname']);
        $this->assertSame($profile->lastname, $profileData['lastname']);
        $this->assertSame($profile->image_original_name, $profileData['image_original_name']);
        $this->assertStringStartsWith('profile/image', $profileData['image_name']);
        $this->assertSame($profile->status, $profileData['status']);
        $this->assertSame($profile->user_id, $profileData['user_id']);
    }

    public function test_cannot_store_profile_while_unauthenticated(): void
    {
        /* @var Profile $profile We make a profile */
        $profile = Profile::factory()->make();

        // We retrieve the json from the endpoint
        $this->post(route('profile.store'), [
            'firstname' => $profile->firstname,
            'lastname' => $profile->lastname,
            'image' => UploadedFile::fake()->createWithContent(
                $profile->image_original_name,
                File::get('/tmp/'.$profile->image_name)
            ),
            'status' => $profile->status,
        ])->assertUnauthorized();
    }
}
