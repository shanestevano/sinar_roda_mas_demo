<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CashierShift;
use App\Models\Inbound;
use App\Models\RackLocation;
use App\Models\Tire;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Kasir role goes straight to POS
        if ($user->role === 'kasir') {
            return redirect()->route('pos.index');
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // 1. General Metrics
        $totalTiresCount = Tire::count();
        $totalStockQty = (int) Tire::sum('stock');
        $lowStockTires = Tire::whereColumn('stock', '<=', 'min_stock')->get();
        $agedDotTires = Tire::agedDot(2)->get();

        // Financial & Sales Aggregations
        $todaySales = (float) Transaction::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('grand_total');

        $monthSales = (float) Transaction::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->sum('grand_total');

        $monthTransactionsCount = Transaction::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->count();

        // Gross Profit Calculation for This Month
        $monthTransactions = Transaction::with('items')
            ->where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->get();

        $monthGrossProfit = $monthTransactions->sum(fn ($tx) => $tx->gross_profit);

        // Inventory Asset Value (Stock * Cost Price)
        $totalInventoryValue = (float) Tire::select(DB::raw('SUM(stock * cost_price) as total_val'))->value('total_val');

        // 7-day Sales Trend for Chart
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyTotal = (float) Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('grand_total');
            $dailyCount = Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->count();

            $salesTrend[] = [
                'date' => $date->format('d M'),
                'day' => $date->locale('id')->isoFormat('ddd'),
                'total' => $dailyTotal,
                'count' => $dailyCount,
            ];
        }

        // Top Selling Tires
        $topTires = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('tires', 'transaction_items.tire_id', '=', 'tires.id')
            ->where('transactions.status', 'completed')
            ->select(
                'tires.id',
                'tires.brand',
                'tires.pattern',
                'tires.size_display',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.subtotal) as total_revenue')
            )
            ->groupBy('tires.id', 'tires.brand', 'tires.pattern', 'tires.size_display')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Recent Activity and Transactions
        $recentTransactions = Transaction::with(['user', 'items.tire'])
            ->latest()
            ->limit(6)
            ->get();

        $recentInbounds = Inbound::with(['supplier', 'user', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        $rackLocations = RackLocation::withCount('tires')->get();
        $totalUsers = User::count();

        return view('dashboard.index', compact(
            'totalTiresCount',
            'totalStockQty',
            'lowStockTires',
            'agedDotTires',
            'todaySales',
            'monthSales',
            'monthTransactionsCount',
            'monthGrossProfit',
            'totalInventoryValue',
            'salesTrend',
            'topTires',
            'recentTransactions',
            'recentInbounds',
            'recentActivities',
            'rackLocations',
            'totalUsers'
        ));
    }
}
