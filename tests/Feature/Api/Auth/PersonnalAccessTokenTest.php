<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\AccessToken;
use Laravel\Passport\Token;
use Tests\TestCase;
use Webmozart\Assert\Assert;

class PersonnalAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_authenticate_using_a_personnal_access_token(): void
    {
        // We create a user
        $user = User::factory()->create();

        // We create a token
        $result = $user->createToken('test');
        $token = $result->getToken();

        Assert::isInstanceOf($token, Token::class);

        // We create a route to check the auth
        Route::get('/currentAccessToken', function (Request $request) {
            $user = $request->user();

            if ($user) {
                /**
                 * @var AccessToken<string> $currentAccessToken
                 */
                $currentAccessToken = $user->currentAccessToken();
                Assert::isInstanceOf($currentAccessToken, AccessToken::class);

                return $currentAccessToken->toJson();
            }

            return response('', 500);
        })->middleware('auth:api');

        /** @var array{
         *           oauth_access_token_id: string,
         *           oauth_client_id: string,
         *           oauth_user_id: string
         * } $json We retrieve the json from the endpoint
         */
        $json = $this->withToken($result->accessToken)->get('/currentAccessToken')->assertSuccessful()->json();

        // We check that we are correctly authenticated
        $this->assertSame($token->getKey(), $json['oauth_access_token_id']);
        $this->assertSame($this->personalAccessTokenClient->getKey(), $json['oauth_client_id']);
        $this->assertEquals($user->getAuthIdentifier(), $json['oauth_user_id']);
    }
}
