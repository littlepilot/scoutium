<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendInvitationRequest;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationsController extends Controller
{
    public function send(SendInvitationRequest $request)
    {
        $invitation = $request->user()->invitations()->create([
            'invite_code' => Str::random(6)
        ]);

        Mail::to($request->get('email'))
            ->queue(new InvitationMail($invitation));

        return [
            'message' => 'Your invitation has been sent.'
        ];
    }
}
