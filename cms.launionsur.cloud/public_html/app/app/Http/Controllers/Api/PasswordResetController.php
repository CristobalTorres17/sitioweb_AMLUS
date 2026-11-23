<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function request(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $key = 'password-reset:' . $request->ip() . ':' . strtolower($request->input('email'));

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json(['message' => 'Too many requests'], 429);
        }

        $status = Password::sendResetLink($request->only('email'));

        RateLimiter::hit($key, 3600);

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent']);
        }

        throw ValidationException::withMessages(['email' => [trans($status)]]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($request->password),
                    'remember_token' => \Illuminate\Support\Str::random(60),
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset']);
        }

        throw ValidationException::withMessages(['email' => [trans($status)]]);
    }
}
