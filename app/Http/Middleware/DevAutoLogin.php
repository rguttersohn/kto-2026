<?php
// app/Http/Middleware/DevAutoLogin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DevAutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {   

        if (!app()->environment('local')) {
            return $next($request);
        }

        // If already authenticated via API, continue
        if (Auth::check()) {

            return $next($request);

        }

        // Check if there's a Filament session cookie in the request
        // Even though it won't auth us, we can detect its presence
        $sessionCookie = $request->cookie('keeping_track_online_session');
        
        if ($sessionCookie) {
            Log::info('made it past session cookie check');
            // There's a Filament session somewhere, so auto-login
            $user = Auth::guard('web')->user();
            
            // If that didn't work (cross-domain issue), just login first user
            if (!$user) {
                $user = \App\Models\User::first();
            }
            
            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}