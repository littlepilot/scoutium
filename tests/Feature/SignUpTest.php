<?php

namespace Tests\Feature;

use App\Events\UserSignedUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;

    const REGISTER_URL = 'api/register';

    /**
     * A basic feature test example.
     *
     * @param string $email
     * @return void
     * @dataProvider validMailAddresses
     * @test
     */
    public function guest_should_be_able_to_sign_up(string $email)
    {
        $response = $this->post(static::REGISTER_URL, [
            'email' => $email,
            'password' => '123456',
        ]);

        /** @var User $user */
        $user = User::where('email', $email)->first();

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        $this->assertNotNull($user->defaultWallet);

        $response->assertJsonFragment([
            'email' => $email,
        ]);

        $response->assertStatus(201);
    }

    /**
     * @param string $invalidEmail
     * @dataProvider invalidMailAddresses
     * @test
     */
    public function guest_shouldnt_be_able_to_sign_up_with_invalid_email(string $invalidEmail)
    {
        $response = $this->post(static::REGISTER_URL, [
            'email' => $invalidEmail,
            'password' => '123456',
        ]);

        $response->assertJsonValidationErrors('email');

        $response->assertStatus(422);
    }

    /**
     * @param string $email
     * @dataProvider validMailAddresses
     * @test
     */
    public function guest_shouldnt_be_able_to_sign_up_with_exists_email(string $email)
    {
        $user = User::factory()->create(['email' => $email]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        $response = $this->post(static::REGISTER_URL, [
            'email' => $email,
            'password' => '123456',
        ]);

        $response->assertJsonValidationErrors('email');

        $response->assertStatus(422);
    }

    /**
     * @param string $password
     * @dataProvider invalidPasswords
     * @test
     */
    public function guest_shouldnt_be_able_to_sign_up_with_invalid_password($password)
    {
        $response = $this->post(static::REGISTER_URL, [
            'email' => 'oushan16@gmail.com',
            'password' => bcrypt($password),
        ]);

        $response->assertJsonValidationErrors('password');

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function when_user_signed_up_should_be_fired_user_create_event()
    {
        $response = $this->post(static::REGISTER_URL, [
            'email' => 'oushan16@gmail.com',
            'password' => '123456',
        ]);

        $this->assertContainsOnlyInstancesOf(UserSignedUp::class, $this->firedEvents);
    }

    /**
     * @return array
     */
    public function validMailAddresses()
    {
        return [
            ['email' => 'oushan16@gmail.com'],
            ['email' => 'noname@demo.com'],
        ];
    }

    /**
     * @return array
     */
    public function invalidMailAddresses()
    {
        return [
            ['email' => 'free text'],
            ['email' => 'free text@email.com'],
            ['email' => 'onlydomain.com'],
        ];
    }

    /**
     * @return array
     */
    public function invalidPasswords()
    {
        return [
            ['password' => 'free text'],// contains space
            ['password' => '.notValid'], // contains dot
            ['password' => '123'], // too short
            ['password' => '1234567890123456789012345678901'], // too long
            ['password' => 123456], // not string
        ];
    }
}
