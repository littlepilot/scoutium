<?php

namespace Tests\Feature;

use App\Mail\InvitationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendInvitationTest extends TestCase
{
    use RefreshDatabase;

    const SEND_INVITATION_URL = '/api/invitations/send';

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function user_should_be_able_to_invite_friend()
    {
        Mail::fake();
        $user = $this->loginAndReturnUser();
        $this->assertDatabaseCount('invitations', 0);
        $response = $this->post(static::SEND_INVITATION_URL, [
            'email' => 'oushan16@gmail.com'
        ]);
        $this->assertDatabaseCount('invitations', 1);

        Mail::assertQueued(InvitationMail::class);

        $response->json([
            'message' => 'Your invitation has been sent.'
        ]);

        $response->assertStatus(200);
    }

    // TODO: send invitation validations tests

    // TODO: invitation mail html test

    public function loginAndReturnUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $user;
    }
}
