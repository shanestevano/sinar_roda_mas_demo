<?php

namespace App\Http\Controllers;

use App\Helpers\BarcodeHelper;
use App\Models\ActivityLog;
use App\Models\RackLocation;
use App\Models\StockMutation;
use App\Models\Tire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tire::with('rackLocation')->latest();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        if ($request->filled('rim')) {
            $query->where('rim_size', (int) $request->input('rim'));
        }

        if ($request->input('filter') === 'low_stock') {
            $query->lowStock();
        } elseif ($request->input('filter') === 'aged_dot') {
            $query->agedDot(2);
        }

        $tires = $query->paginate(12)->withQueryString();

        $brands = Tire::select('brand')->distinct()->orderBy('brand')->pluck('brand');
        $rims = Tire::select('rim_size')->whereNotNull('rim_size')->distinct()->orderBy('rim_size')->pluck('rim_size');
        $rackLocations = RackLocation::orderBy('code')->get();

        $lowStockCount = Tire::lowStock()->count();
        $agedDotCount = Tire::agedDot(2)->count();

        return view('products.index', compact(
            'tires',
            'brands',
            'rims',
            'rackLocations',
            'lowStockCount',
            'agedDotCount'
        ));
    }

    public function create(): View
    {
        $rackLocations = RackLocation::orderBy('code')->get();
        $brands = ['Bridgestone', 'Michelin', 'Dunlop', 'GT Radial', 'Goodyear', 'Hankook', 'Continental', 'Pirelli', 'Yokohama', 'Giti', 'Accelera'];

        return view('products.create', compact('rackLocations', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:tires,sku'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:tires,barcode'],
            'brand' => ['required', 'string', 'max:50'],
            'pattern' => ['required', 'string', 'max:100'],
            'width' => ['nullable', 'integer', 'min:50', 'max:400'],
            'aspect_ratio' => ['nullable', 'integer', 'min:20', 'max:100'],
            'rim_size' => ['nullable', 'integer', 'min:10', 'max:30'],
            'size_display' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'dot_code' => ['nullable', 'string', 'max:10'],
            'dot_year' => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'rack_location_id' => ['nullable', 'exists:rack_locations,id'],
            'notes' => ['nullable', 'string'],
        ]);

        // Auto-generate barcode if empty
        if (empty($validated['barcode'])) {
            $validated['barcode'] = '899' . str_pad((string) mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        }

        // Auto calculate dot_year from 4-digit DOT code (e.g., 3224 -> 2024)
        if (! empty($validated['dot_code']) && strlen($validated['dot_code']) === 4 && empty($validated['dot_year'])) {
            $yearSuffix = substr($validated['dot_code'], 2, 2);
            $validated['dot_year'] = 2000 + (int) $yearSuffix;
        }

        $tire = Tire::create($validated);

        // Record initial stock mutation if stock > 0
        if ($tire->stock > 0) {
            StockMutation::create([
                'tire_id' => $tire->id,
                'type' => 'inbound',
                'quantity' => $tire->stock,
                'stock_before' => 0,
                'stock_after' => $tire->stock,
                'reference_type' => 'InitialStock',
                'reference_id' => $tire->id,
                'user_id' => $request->user()->id,
                'notes' => 'Pencatatan saldo awal stok ban master data.',
            ]);
        }

        ActivityLog::record('CREATE_TIRE', "Menambahkan master data ban baru: {$tire->brand} {$tire->pattern} ({$tire->sku})", [
            'tire_id' => $tire->id,
            'sku' => $tire->sku,
            'initial_stock' => $tire->stock,
        ]);

        return redirect()->route('products.index')->with('success', "Master data ban {$tire->sku} berhasil ditambahkan!");
    }

    public function edit(Tire $tire): View
    {
        $rackLocations = RackLocation::orderBy('code')->get();
        $brands = ['Bridgestone', 'Michelin', 'Dunlop', 'GT Radial', 'Goodyear', 'Hankook', 'Continental', 'Pirelli', 'Yokohama', 'Giti', 'Accelera'];

        return view('products.edit', compact('tire', 'rackLocations', 'brands'));
    }

    public function update(Request $request, Tire $tire): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:tires,sku,' . $tire->id],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:tires,barcode,' . $tire->id],
            'brand' => ['required', 'string', 'max:50'],
            'pattern' => ['required', 'string', 'max:100'],
            'width' => ['nullable', 'integer', 'min:50', 'max:400'],
            'aspect_ratio' => ['nullable', 'integer', 'min:20', 'max:100'],
            'rim_size' => ['nullable', 'integer', 'min:10', 'max:30'],
            'size_display' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'dot_code' => ['nullable', 'string', 'max:10'],
            'dot_year' => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'rack_location_id' => ['nullable', 'exists:rack_locations,id'],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        // Recalculate dot_year if changed
        if (! empty($validated['dot_code']) && strlen($validated['dot_code']) === 4 && empty($validated['dot_year'])) {
            $yearSuffix = substr($validated['dot_code'], 2, 2);
            $validated['dot_year'] = 2000 + (int) $yearSuffix;
        }

        $tire->update($validated);

        ActivityLog::record('UPDATE_TIRE', "Memperbarui master data ban: {$tire->brand} {$tire->pattern} ({$tire->sku})", [
            'tire_id' => $tire->id,
            'sku' => $tire->sku,
        ]);

        return redirect()->route('products.index')->with('success', "Data ban {$tire->sku} berhasil diperbarui!");
    }

    public function destroy(Tire $tire): RedirectResponse
    {
        $sku = $tire->sku;
        // Instead of hard deleting if transactions exist, toggle active
        if ($tire->transactionItems()->exists() || $tire->mutations()->exists()) {
            $tire->update(['is_active' => false]);
            $msg = "Ban {$sku} memiliki riwayat transaksi/mutasi, sehingga status dinonaktifkan (diarsipkan).";
        } else {
            $tire->delete();
            $msg = "Data ban {$sku} berhasil dihapus.";
        }

        ActivityLog::record('DELETE_TIRE', "Menghapus/menonaktifkan ban: {$sku}");

        return redirect()->route('products.index')->with('success', $msg);
    }

    /**
     * Display printable barcode label for a tire
     */
    public function barcode(Tire $tire): View
    {
        $barcodeSvg = BarcodeHelper::generateSvg($tire->barcode ?: $tire->sku, 45, 2);

        return view('products.barcode', compact('tire', 'barcodeSvg'));
    }

    /**
     * Stock adjustment / Stock Opname
     */
    public function adjustStock(Request $request, Tire $tire): RedirectResponse
    {
        $validated = $request->validate([
            'actual_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $newStock = (int) $validated['actual_stock'];
        $stockBefore = $tire->stock;
        $diff = $newStock - $stockBefore;

        if ($diff === 0) {
            return back()->with('info', 'Jumlah stok fisik sama dengan stok sistem. Tidak ada perubahan.');
        }

        $tire->update(['stock' => $newStock]);

        StockMutation::create([
            'tire_id' => $tire->id,
            'type' => 'adjustment',
            'quantity' => $diff,
            'stock_before' => $stockBefore,
            'stock_after' => $newStock,
            'reference_type' => 'StockOpname',
            'reference_id' => $tire->id,
            'user_id' => $request->user()->id,
            'notes' => "Stock Opname: {$validated['reason']}. Penyesuaian {$diff} unit.",
        ]);

        ActivityLog::record('STOCK_OPNAME', "Stock Opname {$tire->sku}: {$stockBefore} -> {$newStock} unit ({$validated['reason']})");

        return back()->with('success', "Penyesuaian stok untuk {$tire->sku} berhasil disimpan (Stok baru: {$newStock})!");
    }
}
