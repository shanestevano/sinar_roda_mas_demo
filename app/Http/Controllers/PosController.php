<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CashierShift;
use App\Models\Setting;
use App\Models\StockMutation;
use App\Models\Tire;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Check active shift for current cashier
        $currentShift = CashierShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        // Quick tires for fast buttons
        $popularTires = Tire::with('rackLocation')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('brand')
            ->limit(12)
            ->get();

        $storeName = Setting::get('store_name', 'Sinar Roda Mas');
        $storeAddress = Setting::get('store_address', 'Jl. Otomotif Raya');

        return view('pos.index', compact('currentShift', 'popularTires', 'storeName', 'storeAddress'));
    }

    /**
     * Smart Search endpoint for tires with eager loading
     */
    public function searchTires(Request $request): JsonResponse
    {
        $term = $request->input('q', '');

        $query = Tire::with('rackLocation')
            ->where('is_active', true);

        if (! empty($term)) {
            $query->search($term);
        } else {
            $query->where('stock', '>', 0)->limit(15);
        }

        $tires = $query->limit(20)->get()->map(function (Tire $tire) {
            return [
                'id' => $tire->id,
                'sku' => $tire->sku,
                'barcode' => $tire->barcode,
                'brand' => $tire->brand,
                'pattern' => $tire->pattern,
                'size_display' => $tire->size_display,
                'type' => $tire->type,
                'dot_code' => $tire->dot_code,
                'dot_year' => $tire->dot_year,
                'selling_price' => (float) $tire->selling_price,
                'formatted_price' => $tire->formatted_selling_price,
                'stock' => $tire->stock,
                'rack_code' => $tire->rackLocation ? $tire->rackLocation->code : '-',
                'rack_zone' => $tire->rackLocation ? $tire->rackLocation->zone : '-',
                'is_low_stock' => $tire->isLowStock(),
                'is_aged' => $tire->isAgedOrExpired(2),
            ];
        });

        return response()->json($tires);
    }

    /**
     * Checkout POS wrapped in DB::transaction()
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'vehicle_plate' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['required', 'string', 'in:cash,transfer,qris,debit'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.tire_id' => ['required', 'exists:tires,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $user = $request->user();

        // Get open shift or auto create one
        $currentShift = CashierShift::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'open'],
            [
                'shift_date' => Carbon::today()->toDateString(),
                'opened_at' => Carbon::now(),
                'starting_cash' => 0,
                'notes' => 'Shift kasir otomatis.',
            ]
        );

        try {
            $transaction = DB::transaction(function () use ($validated, $user, $currentShift) {
                // Generate Collision-Proof Invoice Number: INV-YYYYMMDD-XXXX
                $datePrefix = Carbon::now()->format('Ymd');
                $latestTx = Transaction::where('invoice_number', 'like', "INV-{$datePrefix}-%")
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();

                $sequence = 1;
                if ($latestTx && preg_match('/-(\d+)$/', $latestTx->invoice_number, $matches)) {
                    $sequence = (int) $matches[1] + 1;
                }

                $invoiceNumber = sprintf('INV-%s-%04d', $datePrefix, $sequence);
                while (Transaction::where('invoice_number', $invoiceNumber)->exists()) {
                    $sequence++;
                    $invoiceNumber = sprintf('INV-%s-%04d', $datePrefix, $sequence);
                }

                $subtotal = 0;
                $itemsToInsert = [];

                foreach ($validated['items'] as $itemData) {
                    // Lock tire row for update to prevent race condition
                    $tire = Tire::where('id', $itemData['tire_id'])->lockForUpdate()->first();

                    if (! $tire) {
                        throw new Exception("Data ban ID {$itemData['tire_id']} tidak ditemukan.");
                    }

                    $qtyRequested = (int) $itemData['quantity'];
                    if ($tire->stock < $qtyRequested) {
                        throw new Exception("Stok untuk {$tire->brand} {$tire->pattern} ({$tire->size_display}) tidak mencukupi! Sisa: {$tire->stock}, Diminta: {$qtyRequested}");
                    }

                    $unitPrice = (float) $itemData['unit_price'];
                    $itemSubtotal = $unitPrice * $qtyRequested;
                    $subtotal += $itemSubtotal;

                    $itemsToInsert[] = [
                        'tire' => $tire,
                        'quantity' => $qtyRequested,
                        'cost_price' => (float) $tire->cost_price,
                        'unit_price' => $unitPrice,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $discount = (float) ($validated['discount_amount'] ?? 0);
                $grandTotal = max(0, $subtotal - $discount);
                $amountPaid = (float) $validated['amount_paid'];

                if ($validated['payment_method'] === 'cash' && $amountPaid < $grandTotal) {
                    throw new Exception("Nominal pembayaran tunai kurang dari total belanja! Total: Rp " . number_format($grandTotal, 0, ',', '.') . ", Diterima: Rp " . number_format($amountPaid, 0, ',', '.'));
                }

                $changeAmount = ($validated['payment_method'] === 'cash') ? max(0, $amountPaid - $grandTotal) : 0;
                if ($validated['payment_method'] !== 'cash') {
                    $amountPaid = $grandTotal;
                }

                // 1. Create Transaction
                $tx = Transaction::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => $user->id,
                    'shift_id' => $currentShift->id,
                    'customer_name' => !empty($validated['customer_name']) ? $validated['customer_name'] : 'Pelanggan Umum',
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'vehicle_plate' => !empty($validated['vehicle_plate']) ? strtoupper($validated['vehicle_plate']) : null,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount' => 0,
                    'grand_total' => $grandTotal,
                    'payment_method' => $validated['payment_method'],
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'status' => 'completed',
                ]);

                // 2. Insert items and mutate stock
                foreach ($itemsToInsert as $ins) {
                    /** @var Tire $tire */
                    $tire = $ins['tire'];
                    $qty = $ins['quantity'];

                    TransactionItem::create([
                        'transaction_id' => $tx->id,
                        'tire_id' => $tire->id,
                        'quantity' => $qty,
                        'cost_price' => $ins['cost_price'],
                        'unit_price' => $ins['unit_price'],
                        'subtotal' => $ins['subtotal'],
                    ]);

                    $stockBefore = $tire->stock;
                    $stockAfter = $stockBefore - $qty;

                    // Reduce stock
                    $tire->update(['stock' => $stockAfter]);

                    // Record Stock Mutation
                    StockMutation::create([
                        'tire_id' => $tire->id,
                        'type' => 'sale',
                        'quantity' => -$qty,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reference_type' => 'Transaction',
                        'reference_id' => $tx->id,
                        'user_id' => $user->id,
                        'notes' => "Penjualan POS Invoice {$tx->invoice_number} ({$tx->customer_name})",
                    ]);
                }

                // 3. Update Shift Aggregations
                $currentShift->increment('total_transactions');
                $currentShift->increment('total_sales', $grandTotal);
                if ($tx->payment_method === 'cash') {
                    $currentShift->increment('total_cash_sales', $grandTotal);
                    $currentShift->increment('expected_cash', $grandTotal);
                }

                ActivityLog::record('POS_CHECKOUT', "Transaksi POS berhasil: {$tx->invoice_number} senilai {$tx->formatted_grand_total}", [
                    'transaction_id' => $tx->id,
                    'invoice' => $tx->invoice_number,
                    'items_count' => count($itemsToInsert),
                ]);

                return $tx->load(['items.tire', 'user']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'transaction' => $transaction,
                'receipt_url' => route('pos.receipt', $transaction->id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Printable thermal receipt view
     */
    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['items.tire.rackLocation', 'user', 'voidedByUser']);
        $storeName = Setting::get('store_name', 'Sinar Roda Mas');
        $storeTagline = Setting::get('store_tagline', 'Pusat Ban & Servis Otomotif');
        $storeAddress = Setting::get('store_address', 'Jl. Otomotif Raya No. 88');
        $storePhone = Setting::get('store_phone', '021-5558989');
        $receiptFooter = Setting::get('receipt_footer', 'Terima kasih atas kunjungan Anda.');

        return view('pos.receipt', compact(
            'transaction',
            'storeName',
            'storeTagline',
            'storeAddress',
            'storePhone',
            'receiptFooter'
        ));
    }

    /**
     * Void transaction with Security PIN
     */
    public function void(Request $request, Transaction $transaction): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'string'],
            'reason' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        if ($transaction->status === 'voided') {
            return response()->json(['success' => false, 'message' => 'Transaksi ini sudah pernah dibatalkan (void).'], 422);
        }

        $user = $request->user();
        $enteredPin = trim($validated['pin']);

        // Check PIN against Owner or Admin or default_void_pin
        $defaultPin = Setting::get('default_void_pin', '123456');
        $ownerUser = User::whereIn('role', ['owner', 'admin'])->where('void_pin', $enteredPin)->first();

        $pinIsValid = ($enteredPin === $defaultPin) || ($ownerUser !== null) || ($user->void_pin === $enteredPin);

        if (! $pinIsValid) {
            return response()->json([
                'success' => false,
                'message' => 'PIN Keamanan salah! Pembatalan transaksi membutuhkan PIN Otorisasi Supervisor/Owner.',
            ], 403);
        }

        try {
            DB::transaction(function () use ($transaction, $user, $validated) {
                // Restore each tire's stock
                foreach ($transaction->items as $item) {
                    $tire = Tire::where('id', $item->tire_id)->lockForUpdate()->first();
                    if ($tire) {
                        $stockBefore = $tire->stock;
                        $stockAfter = $stockBefore + $item->quantity;

                        $tire->update(['stock' => $stockAfter]);

                        StockMutation::create([
                            'tire_id' => $tire->id,
                            'type' => 'void_return',
                            'quantity' => $item->quantity,
                            'stock_before' => $stockBefore,
                            'stock_after' => $stockAfter,
                            'reference_type' => 'Transaction',
                            'reference_id' => $transaction->id,
                            'user_id' => $user->id,
                            'notes' => "Pengembalian stok pembatalan/void invoice {$transaction->invoice_number}. Alasan: {$validated['reason']}",
                        ]);
                    }
                }

                // Update transaction status
                $transaction->update([
                    'status' => 'voided',
                    'void_reason' => $validated['reason'],
                    'voided_by' => $user->id,
                    'voided_at' => Carbon::now(),
                ]);

                // Deduct from shift if exists
                if ($transaction->shift_id) {
                    $shift = CashierShift::find($transaction->shift_id);
                    if ($shift && $shift->isOpen()) {
                        $shift->decrement('total_sales', $transaction->grand_total);
                        if ($transaction->payment_method === 'cash') {
                            $shift->decrement('total_cash_sales', $transaction->grand_total);
                            $shift->decrement('expected_cash', $transaction->grand_total);
                        }
                    }
                }

                ActivityLog::record('VOID_TRANSACTION', "Transaksi {$transaction->invoice_number} di-VOID oleh {$user->name}. Alasan: {$validated['reason']}", [
                    'transaction_id' => $transaction->id,
                    'invoice' => $transaction->invoice_number,
                    'grand_total' => $transaction->grand_total,
                    'reason' => $validated['reason'],
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => "Transaksi {$transaction->invoice_number} berhasil dibatalkan (VOID). Stok ban telah dikembalikan ke sistem.",
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Transaction history for Cashier / POS view
     */
    public function history(Request $request): View
    {
        $user = $request->user();
        $query = Transaction::with(['user', 'items.tire'])
            ->latest();

        // If cashier, limit to their own or recent
        if ($user->role === 'kasir') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('vehicle_plate', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('pos.history', compact('transactions'));
    }
}
