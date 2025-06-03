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

class StoreProfileTest extends TestCase
{
    /**
     * @throws FileNotFoundException
     */
    public function test_can_store_profile_while_authenticated(): void
    {
        /* @var Profile $profile We make a profile */
        $profile = Profile::factory()->make();

        // We give the permission to create a profil
        Assert::isInstanceOf($profile->user, User::class);
        $profile->user->givePermissionTo(Permission::query()->where(['name' => 'profile.create', 'guard_name' => 'api'])->firstOrFail());

        // We create a token
        $result = $profile->user->createToken('test');

        Assert::stringNotEmpty($profile->image_original_name);

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
        $json = $this->withToken($result->accessToken)->post(route('profile.store'), [
            'firstname' => $profile->firstname,
            'lastname'  => $profile->lastname,
            'image'     => UploadedFile::fake()->createWithContent(
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

    /**
     * @throws FileNotFoundException
     */
    public function test_cannot_store_profile_while_unauthenticated(): void
    {
        /* @var Profile $profile We make a profile */
        $profile = Profile::factory()->make();

        Assert::stringNotEmpty($profile->image_original_name);

        // We retrieve the json from the endpoint
        $this->post(route('profile.store'), [
            'firstname' => $profile->firstname,
            'lastname'  => $profile->lastname,
            'image'     => UploadedFile::fake()->createWithContent(
                $profile->image_original_name,
                File::get('/tmp/'.$profile->image_name)
            ),
            'status' => $profile->status,
        ])->assertUnauthorized();
    }
}
