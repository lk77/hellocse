<?php

namespace Tests\Feature\Auth;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;
use Laravel\Passport\Database\Factories\ClientFactory;
use Tests\TestCase;

class PersonnalAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_authenticate_using_a_personnal_access_token(): void
    {
        // We create a user
        $user = User::factory()->create();

        /** @var Client $client We create a personal access token client */
        $client = ClientFactory::new()->asPersonalAccessTokenClient()->create();

        // We create a token
        $result = $user->createToken('test');
        $token = $result->getToken();

        // We create a route to check the auth
        Route::get('/currentAccessToken', fn (Request $request) => $request->user()->currentAccessToken()->toJson())->middleware('auth:api');

        // We retrieve the json from the endpoint
        $json = $this->withToken($result->accessToken)->get('/currentAccessToken')->assertSuccessful()->json();

        // We check that we are correctly authenticated
        $this->assertSame($token->getKey(), $json['oauth_access_token_id']);
        $this->assertSame($client->getKey(), $json['oauth_client_id']);
        $this->assertEquals($user->getAuthIdentifier(), $json['oauth_user_id']);
    }
}
