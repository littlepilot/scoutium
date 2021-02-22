<?php

namespace App\Listeners;

use App\Events\UserSignedUp;

class CreateDefaultWallet
{
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
     * @param  UserSignedUp $event
     * @return void
     */
    public function handle(UserSignedUp $event)
    {
        $event->getUser()->wallets()->create([
            'name' => 'My Default Wallet',
            'currency' => 'TRY',
            'balance' => 0,
            'default' => true,
        ]);
    }
}
