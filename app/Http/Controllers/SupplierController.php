<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::withCount('inbounds')->latest()->paginate(12);

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $supplier = Supplier::create($validated);
        ActivityLog::record('CREATE_SUPPLIER', "Menambah supplier baru: {$supplier->name}");

        return redirect()->route('suppliers.index')->with('success', "Supplier {$supplier->name} berhasil ditambahkan!");
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $supplier->update($validated);
        ActivityLog::record('UPDATE_SUPPLIER', "Memperbarui data supplier: {$supplier->name}");

        return redirect()->route('suppliers.index')->with('success', "Data supplier {$supplier->name} berhasil diperbarui!");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->inbounds()->exists()) {
            $supplier->update(['is_active' => false]);

            return redirect()->route('suppliers.index')->with('success', "Supplier {$supplier->name} memiliki riwayat pasokan barang, sehingga status dinonaktifkan.");
        }

        $name = $supplier->name;
        $supplier->delete();
        ActivityLog::record('DELETE_SUPPLIER', "Menghapus supplier: {$name}");

        return redirect()->route('suppliers.index')->with('success', "Supplier {$name} berhasil dihapus.");
    }
}
