<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Profile\Profile;
use App\Models\User\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Webmozart\Assert\Assert;

class UpdateProfileTest extends TestCase
{
    /**
     * @throws FileNotFoundException
     */
    public function test_can_update_profile_while_authenticated(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create();

        /* @var Profile $newProfile We make a new profile */
        $newProfile = Profile::factory()->make();

        // We give the permission to update a profile
        Assert::isInstanceOf($profile->user, User::class);
        $profile->user->givePermissionTo(Permission::query()->where(['name' => 'profile.update', 'guard_name' => 'api'])->firstOrFail());

        // We create a token
        $result = $profile->user->createToken('test');

        Assert::stringNotEmpty($newProfile->image_original_name);

        /** @var array{
         *           data: array{
         *               firstname: string,
         *               lastname: string,
         *               image_original_name: string,
         *               image_name: string,
         *               status: string,
         *               user_id: string
         *           }
         * } $json We retrieve the json from the endpoint
         */
        $json = $this->withToken($result->accessToken)->patch(route('profile.update', compact('profile')), [
            'firstname' => $newProfile->firstname,
            'lastname'  => $newProfile->lastname,
            'image'     => UploadedFile::fake()->createWithContent(
                $newProfile->image_original_name,
                File::get('/tmp/'.$newProfile->image_name)
            ),
            'status' => $newProfile->status,
        ])->assertSuccessful()->json();

        // We recover the profile data
        $profileData = $json['data'];

        // We check the attributes
        $this->assertSame($newProfile->firstname, $profileData['firstname']);
        $this->assertSame($newProfile->lastname, $profileData['lastname']);
        $this->assertSame($newProfile->image_original_name, $profileData['image_original_name']);
        $this->assertStringStartsWith('profile/image', $profileData['image_name']);
        $this->assertSame($newProfile->status, $profileData['status']);

        // The user_id should not have changed
        $this->assertSame($profile->user_id, $profileData['user_id']);
    }

    /**
     * @throws FileNotFoundException
     */
    public function test_cannot_update_profile_while_unauthenticated(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create();

        /* @var Profile $newProfile We make a new profile */
        $newProfile = Profile::factory()->make();

        // We retrieve the json from the endpoint
        Assert::stringNotEmpty($newProfile->image_original_name);
        $this->patch(route('profile.update', compact('profile')), [
            'firstname' => $newProfile->firstname,
            'lastname'  => $newProfile->lastname,
            'image'     => UploadedFile::fake()->createWithContent(
                $newProfile->image_original_name,
                File::get('/tmp/'.$newProfile->image_name)
            ),
            'status' => $newProfile->status,
        ])->assertUnauthorized();
    }
}
