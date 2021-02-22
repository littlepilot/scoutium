<?php

namespace App\Http\Controllers;

use App\Events\UserSignedUp;
use App\Http\Requests\SignUpRequest;
use App\Http\Resources\UserResource;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SignUpController extends Controller
{
    public function index(SignUpRequest $request)
    {
        /** @var User $user */
        $user = User::create([
            'email' => $request->get('email'),
            'name' => $request->get('name', null),
            'password' => bcrypt($request->get('password')),
        ]);

        if ($request->has('invite_code')) {
            /** @var Invitation $invitation */
            $invitation = Invitation::where('invite_code', $request->get('invite_code', null))->first();
            if ($invitation->invitedUser !== null) {
                throw ValidationException::withMessages([
                    'invite_code' => 'Invite code is used.'
                ]);
            }

            $invitation->invitedUser()->associate($user);
            $invitation->save();
        }

        UserSignedUp::dispatch($user);

        return new UserResource($user);
    }
}
