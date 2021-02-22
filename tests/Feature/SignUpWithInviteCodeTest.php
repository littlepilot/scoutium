<?php

namespace Tests\Feature;

use App\Listeners\RewardUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignUpWithInviteCodeTest extends TestCase
{
    use RefreshDatabase;

    const REGISTER_URL = 'api/register';

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function guest_should_be_able_to_sign_up_with_invite_code()
    {
        $invitingUser = $this->loginAndReturnUser();

        $inviteResponse = $this->post(SendInvitationTest::SEND_INVITATION_URL, [
            'email' => 'oushan16@gmail.com'
        ]);

        $this->flushHeaders();

        $signUpResponse = $this->post(static::REGISTER_URL, [
            'email' => 'oushan16@gmail.com',
            'password' => '123456',
            'invite_code' => $invitingUser->invitations->first()->invite_code
        ]);

        /** @var User $invitedUser */
        $invitedUser = User::where('email', 'oushan16@gmail.com')->first();
        $this->assertNotNull($invitedUser->invitation);

        // assert reward
        $this->assertEquals(
            RewardUsers::INVITED_USER_REWARD,
            $invitedUser->defaultWallet->balance
        );
        $this->assertEquals(
            RewardUsers::INVITING_USER_REWARD,
            $invitingUser->defaultWallet->balance
        );

        $signUpResponse->assertStatus(201);
    }

    // TODO: invite code validation tests

    public function loginAndReturnUser()
    {
        $response = $this->post(static::REGISTER_URL, [
            'email' => 'demo@gmail.com',
            'password' => '123456',
        ]);
        $user = User::where('email', 'demo@gmail.com')->first();
        $token = $user->createToken('test')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $user;
    }
}
