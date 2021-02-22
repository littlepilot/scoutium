<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    const AUTHENTICATE_URL = 'api/authenticate';

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function user_should_be_able_to_authenticate_with_correct_credintials()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'oushan16@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->post(static::AUTHENTICATE_URL, [
            'email' => 'oushan16@gmail.com',
            'password' => '123456',
            'device_name' => 'test',
        ]);

        $token = $user->tokens->where('name', 'test')->first();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => get_class($user),
            'tokenable_id' => $user->id,
            'token' => $token->token
        ]);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
        ]);

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function user_should_be_able_to_login_with_valid_token()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'oushan16@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $AuthenticateResponse = $this->post(static::AUTHENTICATE_URL, [
            'email' => 'oushan16@gmail.com',
            'password' => '123456',
            'device_name' => 'test',
        ]);

        $token = $AuthenticateResponse->json('access_token');

        $AuthenticateResponse->assertOk();

        // success login check
        $response = $this->get('api/user', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertOk();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function user_shouldnt_be_able_to_login_with_invalid_token()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'oushan16@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $AuthenticateResponse = $this->post(static::AUTHENTICATE_URL, [
            'email' => 'oushan16@gmail.com',
            'password' => '123456',
            'device_name' => 'test',
        ]);

        $AuthenticateResponse->assertOk();

        // fail login check
        $response = $this->get('api/user', [
            'Authorization' => 'Bearer invalidtoken'
        ]);

        $response->assertStatus(401);
    }
}
