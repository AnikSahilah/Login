<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LoginAttempt;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $ipAddress = request()->ip();
        $loginAttempt = LoginAttempt::firstOrNew(['ip_address' => $ipAddress]);
        return view('auth.login', compact('loginAttempt'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $ipAddress = $request->ip();
        $loginAttempt = LoginAttempt::firstOrNew(['ip_address' => $ipAddress]);

        // Check if the IP address is blocked
        if ($loginAttempt->attempts >= 3) {
            return response()->view('errors.404', [], 404); // Show 404 page
        }

        // Calculate wait time based on attempts
        $set = floor($loginAttempt->attempts / 1);
        $waitTimeMinutes = 0;

        if ($set == 1) {
            $waitTimeMinutes = 1;
        } elseif ($set == 2) {
            $waitTimeMinutes = 5;
        }

        $canAttemptLogin = $loginAttempt->last_attempt_at ? Carbon::parse($loginAttempt->last_attempt_at)->addMinutes($waitTimeMinutes)->isPast() : true;

        if (!$canAttemptLogin) {
            return redirect()->back()->withErrors(['message' => 'Terlalu banyak password salah, silahkan coba lagi dalam ' . $waitTimeMinutes . ' menit']);
        }

        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $loginAttempt->attempts = 0; // Reset attempts after successful login
            $loginAttempt->save();
            return redirect()->route('dashboard');
        } else {
            $loginAttempt->attempts += 1;
            $loginAttempt->last_attempt_at = now();
            $loginAttempt->save();

            $set = floor($loginAttempt->attempts / 1);

            if ($loginAttempt->attempts >= 3) {
                return response()->view('errors.404', [], 404); // Show 404 page
            } elseif ($loginAttempt->attempts % 3 == 0) {
                if ($set == 1) {
                    return redirect()->back()->withErrors(['message' => 'Terlalu banyak password salah, silahkan coba lagi dalam 1 menit']);
                } elseif ($set == 2) {
                    return redirect()->back()->withErrors(['message' => 'Terlalu banyak password salah, silahkan coba lagi dalam 5 menit']);
                } elseif ($set == 3) {
                    return response()->view('errors.404', [], 404); // Show 404 page
                }
            }

            return redirect()->back()->withErrors(['message' => 'Email atau password salah']);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
