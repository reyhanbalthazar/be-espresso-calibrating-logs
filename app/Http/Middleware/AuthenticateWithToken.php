<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $accessToken = PersonalAccessToken::where('token', hash('sha256', $token))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$accessToken) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        // Update last used at
        $accessToken->update(['last_used_at' => now()]);

        // Attach user to request
        $user = $accessToken->tokenable;
        auth()->setUser($user);

        return $next($request);
    }
}
