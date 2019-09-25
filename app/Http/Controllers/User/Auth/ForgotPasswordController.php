<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\ForgotPasswordFormRequest;
use App\Notifications\User\Auth\PasswordResetNotification;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(ForgotPasswordFormRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $passwordReset = PasswordReset::updateOrCreate(
            [
                'email' => $user->email
            ],
            [
                'email' => $user->email,
                'token' => Str::random(60)
             ]
        );

        if ($passwordReset) {
            $user->notify(
                new PasswordResetNotification($passwordReset->token)
            );
        }

        return response()->json([
            'message' => trans('passwords.sent')
        ], 201);
    }
}
