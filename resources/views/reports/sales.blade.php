<x-app-layout title="Laporan Penjualan & Laba Rugi">
    <div class="space-y-6">
        
        <!-- Header & Date Filter Bar -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-black text-white tracking-tight">Laporan Penjualan & Laba Rugi (Owner)</h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Analisis pendapatan kotor, beban modal ban (HPP), serta marjin laba riil toko.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Export to CSV / Excel (PRD 4.2) -->
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
                       class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Ekspor CSV / Excel</span>
                    </a>
                </div>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-4 border-t border-slate-800 text-xs">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Metode Bayar</label>
                    <select name="payment_method" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white">
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                        <option value="transfer" {{ $paymentMethod === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="debit" {{ $paymentMethod === 'debit' ? 'selected' : '' }}>Kartu Debit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kasir</label>
                    <select name="cashier_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white">
                        <option value="">Semua Kasir</option>
                        @foreach($cashiers as $c)
                            <option value="{{ $c->id }}" {{ $cashierId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl transition">
                        Terapkan
                    </button>
                    <a href="{{ route('reports.sales') }}" class="py-2 px-3 bg-slate-800 text-slate-400 hover:text-white rounded-xl">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Pendapatan (Omset)</span>
                <div class="text-2xl font-black text-white">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">{{ $totalCompletedCount }} transaksi sukses</span>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Biaya Modal (HPP)</span>
                <div class="text-2xl font-black text-slate-300">
                    Rp {{ number_format($totalCost, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">Beban pokok perolehan ban</span>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Laba Kotor (Gross Profit)</span>
                <div class="text-2xl font-black text-emerald-400">
                    Rp {{ number_format($grossProfit, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-emerald-300 mt-1 block">Omset dikurangi HPP</span>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Marjin Laba Kotor (%)</span>
                <div class="text-2xl font-black text-purple-400">
                    {{ number_format($profitMargin, 1) }}%
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">Rasio keuntungan penjualan</span>
            </div>
        </div>

        <!-- Sales Data Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 border-b border-slate-800 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">No Faktur & Tanggal</th>
                            <th class="py-4 px-6">Kasir</th>
                            <th class="py-4 px-6">Pelanggan</th>
                            <th class="py-4 px-6">Metode</th>
                            <th class="py-4 px-6 text-right">Modal (HPP)</th>
                            <th class="py-4 px-6 text-right">Penjualan</th>
                            <th class="py-4 px-6 text-right">Laba Kotor</th>
                            <th class="py-4 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-800/40 transition {{ $tx->isVoided() ? 'opacity-50' : '' }}">
                                <td class="py-4 px-6">
                                    <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank"
                                       class="font-mono font-bold text-amber-400 hover:underline block text-xs">
                                        {{ $tx->invoice_number }}
                                    </a>
                                    <span class="text-[10px] text-slate-500">{{ $tx->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-white">{{ $tx->user ? $tx->user->name : '-' }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="text-white">{{ $tx->customer_name }}</div>
                                    @if($tx->vehicle_plate)
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $tx->vehicle_plate }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-semibold uppercase text-[10px] text-slate-400">
                                    {{ $tx->payment_method }}
                                </td>
                                <td class="py-4 px-6 text-right text-slate-400 font-medium">
                                    Rp {{ number_format($tx->total_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-white">
                                    {{ $tx->formatted_grand_total }}
                                </td>
                                <td class="py-4 px-6 text-right font-black {{ $tx->isVoided() ? 'text-slate-500' : 'text-emerald-400' }}">
                                    Rp {{ number_format($tx->gross_profit, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($tx->isCompleted())
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            SUKSES
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30"
                                              title="Alasan: {{ $tx->void_reason }}">
                                            VOID
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500 text-xs">
                                    Tidak ada data penjualan pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-800">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
