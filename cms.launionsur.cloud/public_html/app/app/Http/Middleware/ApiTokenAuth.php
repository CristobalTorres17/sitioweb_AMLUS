<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if ($header && str_starts_with($header, 'Bearer ')) {
            $plain = substr($header, 7);
            $hashed = hash('sha256', $plain);
            $token = ApiToken::where('token', $hashed)->first();

            // Verify token exists, not expired, and user exists in DB
            if ($token && (!$token->expires_at || $token->expires_at->gt(Carbon::now()))) {
                $user = User::find($token->user_id);
                if ($user) {
                    Auth::login($user);
                    return $next($request);
                }
            }
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
