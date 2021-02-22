<?php

namespace App\Listeners;

use App\Events\UserSignedUp;
use App\Models\Invitation;
use App\Models\User;

class RewardUsers
{
    const INVITED_USER_REWARD = 30.0;
    const INVITING_USER_REWARD = 50.0;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserSignedUp $event)
    {
        $invitedUser = $event->getUser();
        /** @var Invitation $invitation */
        $invitation = Invitation::where('invited_user_id', $invitedUser->id)->first();

        if ($invitation === null) {
            return;
        }

        /** @var User $invitingUser */
        $invitingUser = $invitation->invitingUser;

        // reward
        $invitedUser->defaultWallet->reward(static::INVITED_USER_REWARD, 'Ödülü kazandınız.');
        $invitingUser->defaultWallet->reward(static::INVITING_USER_REWARD, 'Ödülü kazandınız.');
    }
}
