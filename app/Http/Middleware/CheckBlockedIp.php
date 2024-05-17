<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LoginAttempt;
use Carbon\Carbon;

class CheckBlockedIp
{
    public function handle(Request $request, Closure $next)
    {
        $ipAddress = $request->ip();
        $loginAttempt = LoginAttempt::firstOrNew(['ip_address' => $ipAddress]);

        if ($loginAttempt->attempts >= 9) { // After 3 attempts, wait 1 minute. After 6 attempts, wait 2 minutes.
            if (Carbon::parse($loginAttempt->last_attempt_at)->diffInMinutes(Carbon::now()) < ($loginAttempt->attempts == 9 ? 1 : 2)) {
                return redirect()->back()->withErrors(['message' => 'IP Anda telah diblokir. Silakan hubungi administrator.']);
            } else {
                $loginAttempt->attempts = 0;
            }
        }

        return $next($request);
    }
}

