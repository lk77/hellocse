<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Client;
use Laravel\Passport\Database\Factories\ClientFactory;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected Client $personalAccessTokenClient;

    protected function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->artisan('db:seed');

        // We create a personal access token client
        $this->personalAccessTokenClient = ClientFactory::new()->asPersonalAccessTokenClient()->createOne();
    }
}
