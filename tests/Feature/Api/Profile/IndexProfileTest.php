<?php

namespace Tests\Feature\Api\Profile;

use App\Enums\Profile\ProfileStatus;
use App\Models\Profile\Profile;
use App\Models\User\User;
use Tests\TestCase;

class IndexProfileTest extends TestCase
{
    public function test_can_get_profiles_while_authenticated(): void
    {
        // We create a user
        $user = User::factory()->create();

        /**
         * @var Profile[] $profiles We create some active profiles
         */
        $profiles = Profile::factory(5)->create(['status' => ProfileStatus::active->name]);
        Profile::factory(5)->create(['status' => ProfileStatus::inactive->name]);

        // We create a token
        $result = $user->createToken('test');

        /** @var array{
         *           data: array<integer, array{
         *               id: string,
         *               firstname: string,
         *               lastname: string,
         *               image_original_name: string,
         *               image_name: string,
         *               status: string,
         *               user_id: string
         *           }>
         * } $json We retrieve the json from the endpoint
         */
        $json = $this->withToken($result->accessToken)->get(route('profile.index'))->json();

        foreach ($profiles as $index => $profile) {
            // We recover the data for that profile
            $profileData = $json['data'][$index];

            // We check the attributes
            $this->assertSame($profile->id, $profileData['id']);
            $this->assertSame($profile->firstname, $profileData['firstname']);
            $this->assertSame($profile->lastname, $profileData['lastname']);
            $this->assertSame($profile->image_original_name, $profileData['image_original_name']);
            $this->assertSame($profile->image_name, $profileData['image_name']);
            $this->assertSame($profile->status, 'active');
            $this->assertSame($profile->user_id, $profileData['user_id']);
        }

        // We check that we have the correct number of profiles
        $this->assertCount(5, $json['data']);
    }

    public function test_can_get_profiles_while_unauthenticated(): void
    {
        /**
         * @var Profile[] $profiles We create some active profiles
         */
        $profiles = Profile::factory(5)->create(['status' => ProfileStatus::active->name]);
        Profile::factory(5)->create(['status' => ProfileStatus::inactive->name]);

        /** @var array{
         *           data: array<integer, array{
         *               id: string,
         *               firstname: string,
         *               lastname: string,
         *               image_original_name: string,
         *               image_name: string,
         *               status: string,
         *               user_id: string
         *           }>
         * } $json We retrieve the json from the endpoint
         */
        $json = $this->get(route('profile.index'))->json();

        foreach ($profiles as $index => $profile) {
            // We recover the data for that profile
            $profileData = $json['data'][$index];

            // We check the attributes
            $this->assertSame($profile->id, $profileData['id']);
            $this->assertSame($profile->firstname, $profileData['firstname']);
            $this->assertSame($profile->lastname, $profileData['lastname']);
            $this->assertSame($profile->image_original_name, $profileData['image_original_name']);
            $this->assertSame($profile->image_name, $profileData['image_name']);
            $this->assertArrayNotHasKey('status', $profileData);
            $this->assertSame($profile->user_id, $profileData['user_id']);
        }

        // We check that we have the correct number of profiles
        $this->assertCount(5, $json['data']);
    }
}
