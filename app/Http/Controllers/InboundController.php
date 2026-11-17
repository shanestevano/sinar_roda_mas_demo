<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inbound;
use App\Models\InboundItem;
use App\Models\StockMutation;
use App\Models\Supplier;
use App\Models\Tire;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InboundController extends Controller
{
    public function index(): View
    {
        $inbounds = Inbound::with(['supplier', 'user', 'items.tire'])
            ->latest()
            ->paginate(12);

        return view('inbounds.index', compact('inbounds'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $tires = Tire::where('is_active', true)->orderBy('brand')->orderBy('size_display')->get();

        return view('inbounds.create', compact('suppliers', 'tires'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_invoice_number' => ['nullable', 'string', 'max:100'],
            'received_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.tire_id' => ['required', 'exists:tires,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
            'items.*.dot_code' => ['nullable', 'string', 'max:10'],
        ]);

        $user = $request->user();

        try {
            $inbound = DB::transaction(function () use ($validated, $user) {
                // Generate Collision-Proof Reference Number: INB-YYYYMMDD-XXXX
                $datePrefix = Carbon::parse($validated['received_date'])->format('Ymd');
                $latestInbound = Inbound::where('reference_number', 'like', "INB-{$datePrefix}-%")
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();

                $sequence = 1;
                if ($latestInbound && preg_match('/-(\d+)$/', $latestInbound->reference_number, $matches)) {
                    $sequence = (int) $matches[1] + 1;
                }

                $refNumber = sprintf('INB-%s-%03d', $datePrefix, $sequence);
                while (Inbound::where('reference_number', $refNumber)->exists()) {
                    $sequence++;
                    $refNumber = sprintf('INB-%s-%03d', $datePrefix, $sequence);
                }

                $totalAmount = 0;
                $itemsData = [];

                foreach ($validated['items'] as $item) {
                    $qty = (int) $item['quantity'];
                    $cost = (float) $item['cost_price'];
                    $subtotal = $qty * $cost;
                    $totalAmount += $subtotal;

                    $itemsData[] = [
                        'tire_id' => $item['tire_id'],
                        'quantity' => $qty,
                        'cost_price' => $cost,
                        'subtotal' => $subtotal,
                        'dot_code' => $item['dot_code'] ?? null,
                    ];
                }

                $inbound = Inbound::create([
                    'reference_number' => $refNumber,
                    'supplier_id' => $validated['supplier_id'],
                    'supplier_invoice_number' => $validated['supplier_invoice_number'] ?? null,
                    'received_date' => $validated['received_date'],
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($itemsData as $item) {
                    InboundItem::create([
                        'inbound_id' => $inbound->id,
                        'tire_id' => $item['tire_id'],
                        'quantity' => $item['quantity'],
                        'cost_price' => $item['cost_price'],
                        'subtotal' => $item['subtotal'],
                        'dot_code' => $item['dot_code'],
                    ]);

                    /** @var Tire $tire */
                    $tire = Tire::where('id', $item['tire_id'])->lockForUpdate()->first();
                    if ($tire) {
                        $stockBefore = $tire->stock;
                        $stockAfter = $stockBefore + $item['quantity'];

                        $updateData = [
                            'stock' => $stockAfter,
                            'cost_price' => $item['cost_price'], // update to latest cost price
                        ];

                        // Update DOT code if provided
                        if (! empty($item['dot_code'])) {
                            $updateData['dot_code'] = $item['dot_code'];
                            if (strlen($item['dot_code']) === 4) {
                                $updateData['dot_year'] = 2000 + (int) substr($item['dot_code'], 2, 2);
                            }
                        }

                        $tire->update($updateData);

                        StockMutation::create([
                            'tire_id' => $tire->id,
                            'type' => 'inbound',
                            'quantity' => $item['quantity'],
                            'stock_before' => $stockBefore,
                            'stock_after' => $stockAfter,
                            'reference_type' => 'Inbound',
                            'reference_id' => $inbound->id,
                            'user_id' => $user->id,
                            'notes' => "Penerimaan barang masuk dari {$inbound->supplier->name} (Ref: {$inbound->reference_number})",
                        ]);
                    }
                }

                ActivityLog::record('INBOUND_GOODS', "Penerimaan barang masuk {$inbound->reference_number} senilai {$inbound->formatted_total_amount}", [
                    'inbound_id' => $inbound->id,
                    'supplier_id' => $inbound->supplier_id,
                    'total_items' => count($itemsData),
                ]);

                return $inbound;
            });

            return redirect()->route('inbounds.show', $inbound->id)->with('success', "Penerimaan barang {$inbound->reference_number} berhasil dicatat dan stok telah bertambah!");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memproses penerimaan barang: ' . $e->getMessage()]);
        }
    }

    public function show(Inbound $inbound): View
    {
        $inbound->load(['supplier', 'user', 'items.tire.rackLocation']);

        return view('inbounds.show', compact('inbound'));
    }
}
