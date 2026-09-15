<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekUserBlokir
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_blocked) {
            $blockedUserId = $user->id_user;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            session(['blocked_user_id' => $blockedUserId]);

            return redirect()->route('blocked.show');
        }

        return $next($request);
    }
}