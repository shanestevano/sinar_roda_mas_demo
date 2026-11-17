<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::latest();

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:admin,owner,gudang,kasir'],
            'phone' => ['nullable', 'string', 'max:30'],
            'void_pin' => ['nullable', 'string', 'max:10'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'void_pin' => $validated['void_pin'] ?? '123456',
            'is_active' => true,
        ]);

        ActivityLog::record('CREATE_USER', "Membuat akun pengguna baru: {$user->name} ({$user->role})");

        return redirect()->route('users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan!");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,owner,gudang,kasir'],
            'phone' => ['nullable', 'string', 'max:30'],
            'void_pin' => ['nullable', 'string', 'max:10'],
        ]);

        $user->update($validated);
        ActivityLog::record('UPDATE_USER', "Memperbarui akun pengguna: {$user->name} ({$user->role})");

        return redirect()->route('users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri!');
        }

        $user->update(['is_active' => ! $user->is_active]);
        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan (diblokir)';

        ActivityLog::record('TOGGLE_USER_STATUS', "Akun pengguna {$user->name} {$statusStr}");

        return redirect()->route('users.index')->with('success', "Status akun {$user->name} berhasil {$statusStr}.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::record('RESET_PASSWORD', "Mereset kata sandi pengguna: {$user->name}");

        return redirect()->route('users.index')->with('success', "Kata sandi untuk {$user->name} berhasil diatur ulang!");
    }
}
