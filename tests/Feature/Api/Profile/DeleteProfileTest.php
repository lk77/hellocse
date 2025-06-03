<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Profile\Profile;
use App\Models\User\User;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Webmozart\Assert\Assert;

class DeleteProfileTest extends TestCase
{
    public function test_can_delete_profile_while_authenticated(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create();

        // We give the permission to delete a profile
        Assert::isInstanceOf($profile->user, User::class);
        $profile->user->givePermissionTo(Permission::query()->where(['name' => 'profile.delete', 'guard_name' => 'api'])->firstOrFail());

        // We create a token
        $result = $profile->user->createToken('test');

        // We delete the profile
        $this->withToken($result->accessToken)->delete(route('profile.destroy', compact('profile')))->assertSuccessful();

        // The profile should be gone
        $this->assertDatabaseMissing('profiles', [
            'id'        => $profile->id,
            'firstname' => $profile->firstname,
            'lastname'  => $profile->lastname,
        ]);
    }

    public function test_cannot_deletee_profile_while_unauthenticated(): void
    {
        /* @var Profile $profile We create a profile */
        $profile = Profile::factory()->create();

        // We retrieve the json from the endpoint
        $this->delete(route('profile.destroy', compact('profile')))->assertUnauthorized();
    }
}
