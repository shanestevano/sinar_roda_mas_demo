<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.']);
            }

            $request->session()->regenerate();
            ActivityLog::record('LOGIN', 'User masuk ke sistem: ' . $user->name . ' (' . strtoupper($user->role) . ')');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function quickLogin(string $role): RedirectResponse
    {
        $validRoles = ['admin', 'owner', 'gudang', 'kasir'];
        if (! in_array($role, $validRoles)) {
            abort(404);
        }

        $user = User::where('role', $role)->where('is_active', true)->first();
        if (! $user) {
            return back()->with('error', "User dengan peran {$role} tidak ditemukan.");
        }

        Auth::login($user);
        request()->session()->regenerate();
        ActivityLog::record('QUICK_LOGIN', "Simulasi login cepat sebagai: {$user->name} ({$role})");

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            ActivityLog::record('LOGOUT', 'User keluar dari sistem: ' . Auth::user()->name);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
