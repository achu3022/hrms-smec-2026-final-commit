<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\EmployeeLogin;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('employee')->check()) {
            if (Auth::guard('employee')->user()->is_first_login) {
                return redirect()->route('employee.password.reset');
            }
            return redirect()->route('employee.dashboard');
        }

        return view('employee.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('employee')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if (Auth::guard('employee')->user()->is_first_login) {
                return redirect()->route('employee.password.reset');
            }

            return redirect()->route('employee.dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('employee.login');
    }

    public function showResetPasswordForm()
    {
        $user = Auth::guard('employee')->user();
        if (!$user->is_first_login) {
            return redirect()->route('employee.dashboard');
        }
        return view('employee.auth.reset-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(6)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $user = Auth::guard('employee')->user();
        
        $user->update([
            'password' => Hash::make($request->password),
            'is_first_login' => false,
        ]);

        return redirect()->route('employee.dashboard')->with('status', 'password-updated');
    }
}
