<x-app-layout title="Dashboard Eksekutif & Operasional">
    <div class="space-y-8">
        <!-- Welcome Hero Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950/40 border border-slate-800 p-6 sm:p-8 shadow-xl">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sistem Berjalan Aktif • {{ date('d F Y') }}
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Selamat Datang, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-slate-400 text-sm mt-1 max-w-2xl">
                        Aplikasi otomasi toko & gudang ban <strong class="text-slate-200">Sinar Roda Mas</strong>. 
                        Pantau arus kas kasir, ketersediaan stok fisik di rak gudang, serta siklus tahun produksi ban (DOT) secara real-time.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @if(auth()->user()->hasRole(['kasir', 'admin']))
                    <a href="{{ route('pos.index') }}"
                       class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/25 transition active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Buka Kasir POS</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasRole(['gudang', 'admin']))
                    <a href="{{ route('inbounds.create') }}"
                       class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider border border-slate-700 transition active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Penerimaan Barang</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI Executive Metrics (PRD 4.2) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Penjualan Hari Ini -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Penjualan Hari Ini</span>
                    <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-white">
                    Rp {{ number_format($todaySales, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
                    <span>Transaksi hari ini</span>
                </div>
            </div>

            <!-- Omset Bulan Ini & Laba Kotor -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Omset Bulan Ini</span>
                    <span class="p-2 rounded-xl bg-purple-500/10 text-purple-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-white">
                    Rp {{ number_format($monthSales, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-purple-300 mt-2 font-medium">
                    Estimasi Laba Kotor: <strong>Rp {{ number_format($monthGrossProfit, 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Total Nilai Aset Stok Ban -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Nilai Aset Stok Ban</span>
                    <span class="p-2 rounded-xl bg-blue-500/10 text-blue-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-white">
                    Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2">
                    Total: <strong class="text-white">{{ $totalStockQty }} unit</strong> ban ({{ $totalTiresCount }} SKU)
                </div>
            </div>

            <!-- Peringatan Dead Stock & Stok Menipis -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Peringatan Gudang</span>
                    <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div>
                        <div class="text-2xl font-black text-rose-400">{{ $lowStockTires->count() }}</div>
                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Stok Kritis</div>
                    </div>
                    <div class="w-px h-8 bg-slate-800"></div>
                    <div>
                        <div class="text-2xl font-black text-amber-400">{{ $agedDotTires->count() }}</div>
                        <div class="text-[10px] text-slate-400 uppercase font-semibold">DOT Lama (&ge;2 Thn)</div>
                    </div>
                </div>
                <div class="text-[11px] text-slate-400 mt-2">
                    <a href="{{ route('reports.dead-stock') }}" class="text-amber-400 hover:underline">Periksa laporan ban &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Sales Trend Chart & Top Selling Tires (PRD 4.2) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 7-day Sales Bar Chart -->
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Tren Penjualan 7 Hari Terakhir</h2>
                        <p class="text-xs text-slate-400">Pergerakan total omset harian kasir</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-xs text-slate-300 font-medium">
                        Real-time Data
                    </span>
                </div>

                @php
                    $maxVal = max(1, max(array_column($salesTrend, 'total')));
                @endphp
                <div class="h-64 flex items-end gap-3 pt-6 px-2">
                    @foreach($salesTrend as $day)
                        @php
                            $heightPercent = max(6, round(($day['total'] / $maxVal) * 100));
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 group relative">
                            <!-- Tooltip -->
                            <div class="absolute -top-12 bg-slate-800 text-white border border-slate-700 text-[10px] px-2.5 py-1.5 rounded-xl opacity-0 group-hover:opacity-100 transition shadow-xl pointer-events-none whitespace-nowrap z-20">
                                <div class="font-bold">Rp {{ number_format($day['total'], 0, ',', '.') }}</div>
                                <div class="text-slate-400">{{ $day['count'] }} Transaksi</div>
                            </div>

                            <div class="w-full bg-slate-800/80 rounded-xl overflow-hidden flex flex-col justify-end h-48 border border-slate-700/60 group-hover:border-amber-500/50 transition">
                                <div style="height: {{ $heightPercent }}%;"
                                     class="w-full bg-gradient-to-t from-amber-600 to-amber-400 rounded-t-xl group-hover:from-amber-500 group-hover:to-amber-300 transition-all duration-300">
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-400 group-hover:text-white transition">
                                {{ $day['date'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Selling Tires -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Ban Paling Laris</h2>
                        <p class="text-xs text-slate-400">Model terpopuler bulan ini</p>
                    </div>
                </div>

                <div class="space-y-3 mt-4">
                    @forelse($topTires as $index => $t)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-800/40 border border-slate-800 hover:border-slate-700 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-300 font-black text-xs flex items-center justify-center flex-shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-white truncate">{{ $t->brand }} {{ $t->pattern }}</h3>
                                    <p class="text-[11px] text-slate-400">{{ $t->size_display }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-black text-emerald-400 block">{{ $t->total_qty }} unit</span>
                                <span class="text-[10px] text-slate-400">Rp {{ number_format($t->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-slate-500 text-xs">
                            Belum ada data penjualan tercatat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Transactions & Warehouse Inbounds (Two columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Transaksi Kasir Terbaru -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Transaksi Kasir Terkini</h2>
                        <p class="text-xs text-slate-400">Penjualan langsung toko lini depan</p>
                    </div>
                    <a href="{{ route('pos.history') }}" class="text-xs text-amber-400 hover:underline font-semibold">Lihat Semua &rarr;</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentTransactions as $tx)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-800/40 border border-slate-800 hover:border-slate-700 transition">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-amber-400">{{ $tx->invoice_number }}</span>
                                    @if($tx->isVoided())
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40">VOID</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">SUKSES</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-300 mt-1">
                                    {{ $tx->customer_name }} {{ $tx->vehicle_plate ? "• {$tx->vehicle_plate}" : '' }}
                                </p>
                                <span class="text-[10px] text-slate-400">Kasir: {{ $tx->user ? $tx->user->name : '-' }} • {{ $tx->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <div class="text-sm font-bold text-white">{{ $tx->formatted_grand_total }}</div>
                                <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank"
                                   class="text-[10px] text-slate-400 hover:text-amber-400 underline mt-1 inline-block">
                                    Cetak Struk
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500 text-xs">Belum ada transaksi.</div>
                    @endforelse
                </div>
            </div>

            <!-- Inbound Goods Terbaru (Gudang) -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Penerimaan Barang Gudang (Inbound)</h2>
                        <p class="text-xs text-slate-400">Pasokan ban fisik masuk dari distributor</p>
                    </div>
                    @if(auth()->user()->hasRole(['gudang', 'admin']))
                    <a href="{{ route('inbounds.index') }}" class="text-xs text-amber-400 hover:underline font-semibold">Lihat Semua &rarr;</a>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($recentInbounds as $inb)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-800/40 border border-slate-800 hover:border-slate-700 transition">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-blue-400">{{ $inb->reference_number }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $inb->received_date->format('d/m/Y') }}</span>
                                </div>
                                <p class="text-xs text-slate-300 font-medium mt-1 truncate">{{ $inb->supplier ? $inb->supplier->name : '-' }}</p>
                                <span class="text-[10px] text-slate-400">{{ $inb->items->count() }} jenis ban • Diterima: {{ $inb->user ? $inb->user->name : '-' }}</span>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <div class="text-sm font-bold text-emerald-400">{{ $inb->formatted_total_amount }}</div>
                                @if(auth()->user()->hasRole(['gudang', 'admin']))
                                <a href="{{ route('inbounds.show', $inb->id) }}" class="text-[10px] text-slate-400 hover:text-blue-400 underline mt-1 inline-block">
                                    Lihat Rincian
                                </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500 text-xs">Belum ada data barang masuk.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
