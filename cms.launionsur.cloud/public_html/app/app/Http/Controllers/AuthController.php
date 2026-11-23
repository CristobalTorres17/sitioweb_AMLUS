<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $user = User::where('email', $request->input('email'))->first();
            if ($user) {
                $user->locked_until = Carbon::now()->addMinutes(15);
                $user->save();
            }
            RateLimiter::clear($key);
            return response()->json(['message' => 'Too many attempts'], 429);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($key, 900);
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->locked_until && Carbon::now()->lt($user->locked_until)) {
            return response()->json(['message' => 'Account locked'], 423);
        }

        RateLimiter::clear($key);

        $user->last_login_at = Carbon::now();
        $user->save();

        $token = Str::random(80);
        $expires = Carbon::now()->addMinutes(15);

        ApiToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => $expires,
        ]);

        return response()->json(['token' => $token, 'expires_at' => $expires->toDateTimeString()]);
    }

    public function logout(Request $request)
    {
        $header = $request->header('Authorization');
        if ($header && str_starts_with($header, 'Bearer ')) {
            $plain = substr($header, 7);
            ApiToken::where('token', hash('sha256', $plain))->delete();
        }
        return response()->json(['message' => 'Logged out']);
    }

    public function refresh(Request $request)
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $plain = substr($header, 7);
        $hashed = hash('sha256', $plain);
        $tokenRow = ApiToken::where('token', $hashed)->first();
        if (!$tokenRow || $tokenRow->isExpired()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // rotate
        $newPlain = Str::random(80);
        $expires = Carbon::now()->addMinutes(15);
        ApiToken::create([
            'user_id' => $tokenRow->user_id,
            'token' => hash('sha256', $newPlain),
            'expires_at' => $expires,
        ]);
        $tokenRow->delete();

        return response()->json(['token' => $newPlain, 'expires_at' => $expires->toDateTimeString()]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }
}
