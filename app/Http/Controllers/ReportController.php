<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\StockMutation;
use App\Models\Tire;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Sales & Profit/Loss Report
     */
    public function sales(Request $request): View|Response|StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $paymentMethod = $request->input('payment_method');
        $cashierId = $request->input('cashier_id');
        $status = $request->input('status', 'completed');

        $query = Transaction::with(['user', 'items.tire'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($cashierId) {
            $query->where('user_id', $cashierId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Export to CSV if requested
        if ($request->input('export') === 'csv') {
            return $this->exportSalesCsv($query->get(), $startDate, $endDate);
        }

        $transactions = (clone $query)->latest()->paginate(20)->withQueryString();

        // Calculate summary
        $allTx = $query->get();
        $totalRevenue = (float) $allTx->where('status', 'completed')->sum('grand_total');
        $totalCost = (float) $allTx->where('status', 'completed')->sum('total_cost');
        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $totalCompletedCount = $allTx->where('status', 'completed')->count();
        $totalVoidedCount = $allTx->where('status', 'voided')->count();

        $cashiers = User::whereIn('role', ['kasir', 'admin'])->get();

        return view('reports.sales', compact(
            'transactions',
            'startDate',
            'endDate',
            'paymentMethod',
            'cashierId',
            'status',
            'totalRevenue',
            'totalCost',
            'grossProfit',
            'profitMargin',
            'totalCompletedCount',
            'totalVoidedCount',
            'cashiers'
        ));
    }

    /**
     * Stock Movement / Stock Card (Kartu Stok) Report
     */
    public function stockCard(Request $request): View|Response|StreamedResponse
    {
        $tireId = $request->input('tire_id');
        $type = $request->input('type');
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = StockMutation::with(['tire', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($tireId) {
            $query->where('tire_id', $tireId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($request->input('export') === 'csv') {
            return $this->exportStockCardCsv($query->latest()->get(), $startDate, $endDate);
        }

        $mutations = $query->latest()->paginate(25)->withQueryString();
        $tires = Tire::orderBy('brand')->orderBy('size_display')->get();

        return view('reports.stock_card', compact('mutations', 'tires', 'tireId', 'type', 'startDate', 'endDate'));
    }

    /**
     * Dead Stock / Aged DOT Report (Pencegahan Ban Kadaluarsa)
     */
    public function deadStock(Request $request): View
    {
        $maxYears = (int) $request->input('years', 2);
        $tires = Tire::with('rackLocation')
            ->agedDot($maxYears)
            ->orderBy('dot_year')
            ->get();

        $totalAgedUnits = $tires->sum('stock');
        $totalAgedAssetValue = $tires->sum(fn ($t) => $t->stock * (float) $t->cost_price);

        return view('reports.dead_stock', compact('tires', 'maxYears', 'totalAgedUnits', 'totalAgedAssetValue'));
    }

    /**
     * Audit Trail / Activity Log View
     */
    public function auditTrail(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $logs = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('reports.audit_trail', compact('logs', 'users', 'actions'));
    }

    private function exportSalesCsv($transactions, string $start, string $end): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"Laporan_Penjualan_{$start}_sd_{$end}.csv\"",
        ];

        $callback = function () use ($transactions) {
            $output = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, ['No Faktur', 'Tanggal & Waktu', 'Kasir', 'Pelanggan', 'No Polisi', 'Metode Bayar', 'Total Biaya Modal (HPP)', 'Total Penjualan', 'Laba Kotor', 'Status']);

            foreach ($transactions as $tx) {
                fputcsv($output, [
                    $tx->invoice_number,
                    $tx->created_at->format('d/m/Y H:i'),
                    $tx->user ? $tx->user->name : '-',
                    $tx->customer_name,
                    $tx->vehicle_plate ?: '-',
                    strtoupper($tx->payment_method),
                    $tx->total_cost,
                    $tx->grand_total,
                    $tx->gross_profit,
                    strtoupper($tx->status),
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportStockCardCsv($mutations, string $start, string $end): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"Kartu_Stok_{$start}_sd_{$end}.csv\"",
        ];

        $callback = function () use ($mutations) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, ['Waktu', 'SKU Ban', 'Merk & Ukuran', 'Jenis Mutasi', 'Qty Mutasi', 'Stok Awal', 'Stok Akhir', 'Referensi', 'Petugas / User', 'Keterangan']);

            foreach ($mutations as $m) {
                fputcsv($output, [
                    $m->created_at->format('d/m/Y H:i'),
                    $m->tire ? $m->tire->sku : '-',
                    $m->tire ? "{$m->tire->brand} {$m->tire->pattern} ({$m->tire->size_display})" : '-',
                    $m->type_name,
                    $m->quantity,
                    $m->stock_before,
                    $m->stock_after,
                    $m->reference_type ? "{$m->reference_type} #{$m->reference_id}" : '-',
                    $m->user ? $m->user->name : '-',
                    $m->notes ?: '-',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
