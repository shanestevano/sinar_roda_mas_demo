<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CashierShift;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = CashierShift::with('user')->latest();

        if ($user->role === 'kasir') {
            $query->where('user_id', $user->id);
        }

        $shifts = $query->paginate(15);

        // Check if currently logged in user has an active open shift
        $activeShift = CashierShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        return view('shifts.index', compact('shifts', 'activeShift'));
    }

    public function startShift(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Check if user already has open shift
        $existing = CashierShift::where('user_id', $user->id)->where('status', 'open')->first();
        if ($existing) {
            return redirect()->route('pos.index')->with('info', 'Anda sudah memiliki shift yang sedang aktif.');
        }

        $validated = $request->validate([
            'starting_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $startingCash = (float) $validated['starting_cash'];

        $shift = CashierShift::create([
            'user_id' => $user->id,
            'shift_date' => Carbon::today()->toDateString(),
            'opened_at' => Carbon::now(),
            'starting_cash' => $startingCash,
            'expected_cash' => $startingCash,
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLog::record('START_SHIFT', "Kasir {$user->name} membuka shift kasir dengan modal awal Rp " . number_format($startingCash, 0, ',', '.'));

        return redirect()->route('pos.index')->with('success', 'Shift kasir berhasil dibuka! Selamat bertugas.');
    }

    public function closeShift(Request $request, CashierShift $shift): RedirectResponse
    {
        $validated = $request->validate([
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $actualCash = (float) $validated['actual_cash'];
        $expectedCash = (float) $shift->expected_cash;
        $difference = $actualCash - $expectedCash;

        $shift->update([
            'closed_at' => Carbon::now(),
            'actual_cash' => $actualCash,
            'difference' => $difference,
            'status' => 'closed',
            'notes' => $shift->notes . ($validated['notes'] ? " | Catatan Tutup: " . $validated['notes'] : ''),
        ]);

        ActivityLog::record('CLOSE_SHIFT', "Kasir {$shift->user->name} menutup shift. Kas fisik: Rp " . number_format($actualCash, 0, ',', '.') . " (Selisih: Rp " . number_format($difference, 0, ',', '.') . ")");

        return redirect()->route('shifts.index')->with('success', 'Shift kasir berhasil ditutup. Rekapitulasi shift telah tersimpan.');
    }
}
