<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\RackLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RackController extends Controller
{
    public function index(): View
    {
        $rackLocations = RackLocation::with(['tires' => function ($q) {
            $q->orderBy('brand')->orderBy('size_display');
        }])
            ->withCount('tires')
            ->orderBy('code')
            ->get();

        return view('racks.index', compact('rackLocations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:rack_locations,code'],
            'zone' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $rack = RackLocation::create($validated);
        ActivityLog::record('CREATE_RACK', "Menambah lokasi rak gudang baru: {$rack->code} ({$rack->zone})");

        return redirect()->route('racks.index')->with('success', "Lokasi rak {$rack->code} berhasil ditambahkan!");
    }

    public function update(Request $request, RackLocation $rack): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:rack_locations,code,' . $rack->id],
            'zone' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $rack->update($validated);
        ActivityLog::record('UPDATE_RACK', "Memperbarui lokasi rak gudang: {$rack->code}");

        return redirect()->route('racks.index')->with('success', "Data rak {$rack->code} berhasil diperbarui!");
    }

    public function destroy(RackLocation $rack): RedirectResponse
    {
        if ($rack->tires()->count() > 0) {
            return back()->with('error', "Rak {$rack->code} masih memiliki data ban tertaut. Pindahkan ban ke rak lain terlebih dahulu.");
        }

        $code = $rack->code;
        $rack->delete();
        ActivityLog::record('DELETE_RACK', "Menghapus lokasi rak: {$code}");

        return redirect()->route('racks.index')->with('success', "Lokasi rak {$code} berhasil dihapus.");
    }
}
