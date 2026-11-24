<x-app-layout title="Dashboard Utama">
    <div class="space-y-6">
        <!-- Clean Welcome Bar -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-7 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Operasional Aktif • {{ date('d F Y') }}
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Selamat Datang, {{ auth()->user()->name }}
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-2xl">
                    Ringkasan performa penjualan kasir, ketersediaan stok di rak gudang, dan pengawasan usia ban (DOT) toko Sinar Roda Mas.
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-shrink-0">
                @if(auth()->user()->hasRole(['kasir', 'admin']))
                <a href="{{ route('pos.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm transition active:scale-[0.98] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Buka Kasir POS</span>
                </a>
                @endif

                @if(auth()->user()->hasRole(['gudang', 'admin']))
                <a href="{{ route('inbounds.create') }}"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs border border-slate-300 shadow-xs transition active:scale-[0.98] flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Inbound Ban</span>
                </a>
                @endif
            </div>
        </div>

        <!-- KPI Metric Cards (Clean & High Contrast) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- Penjualan Hari Ini -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between text-slate-500 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Penjualan Hari Ini</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-bold text-slate-900 tracking-tight">
                    Rp {{ number_format($todaySales, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-2">
                    Transaksi aktif hari ini
                </div>
            </div>

            <!-- Omset Bulan Ini -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between text-slate-500 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Omset Bulan Ini</span>
                    <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-bold text-slate-900 tracking-tight">
                    Rp {{ number_format($monthSales, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-2">
                    Est. Laba Kotor: <strong class="text-emerald-600 font-semibold">Rp {{ number_format($monthGrossProfit, 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Nilai Aset Stok Ban -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between text-slate-500 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Nilai Aset Stok Ban</span>
                    <span class="p-2 rounded-xl bg-sky-50 text-sky-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-bold text-slate-900 tracking-tight">
                    Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-2">
                    Total: <strong class="text-slate-800 font-semibold">{{ $totalStockQty }} unit</strong> ({{ $totalTiresCount }} SKU)
                </div>
            </div>

            <!-- Peringatan Gudang -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between text-slate-500 text-xs mb-3 font-semibold uppercase tracking-wider">
                    <span>Peringatan Gudang</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div>
                        <div class="text-2xl font-bold text-rose-600">{{ $lowStockTires->count() }}</div>
                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Stok Kritis</div>
                    </div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div>
                        <div class="text-2xl font-bold text-amber-600">{{ $agedDotTires->count() }}</div>
                        <div class="text-[10px] text-slate-400 uppercase font-semibold">DOT &ge;2 Thn</div>
                    </div>
                </div>
                <div class="text-[11px] text-blue-600 font-medium mt-2">
                    <a href="{{ route('reports.dead-stock') }}" class="hover:underline">Periksa laporan ban &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Sales Trend Chart & Top Selling Tires -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 7-day Sales Bar Chart -->
            <div class="lg:col-span-2 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Tren Penjualan 7 Hari Terakhir</h3>
                        <p class="text-xs text-slate-500">Pergerakan total omset harian kasir</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-semibold">
                        Real-time Data
                    </span>
                </div>

                @php
                    $maxVal = max(1, max(array_column($salesTrend, 'total')));
                @endphp
                <div class="h-60 flex items-end gap-3 pt-6 px-2">
                    @foreach($salesTrend as $day)
                        @php
                            $heightPercent = max(6, round(($day['total'] / $maxVal) * 100));
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 group relative">
                            <!-- Tooltip -->
                            <div class="absolute -top-12 bg-slate-900 text-white text-[10px] px-2.5 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition shadow-lg pointer-events-none whitespace-nowrap z-20">
                                <div class="font-bold">Rp {{ number_format($day['total'], 0, ',', '.') }}</div>
                                <div class="text-slate-300">{{ $day['count'] }} Transaksi</div>
                            </div>

                            <div class="w-full bg-slate-100 rounded-lg overflow-hidden flex flex-col justify-end h-44 border border-slate-200/60 group-hover:border-blue-400 transition">
                                <div style="height: {{ $heightPercent }}%;"
                                     class="w-full bg-blue-600 rounded-t-lg group-hover:bg-blue-700 transition-all duration-200">
                                </div>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500 group-hover:text-slate-900 transition">
                                {{ $day['date'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Selling Tires -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Ban Paling Laris</h3>
                        <p class="text-xs text-slate-500">Model terpopuler bulan ini</p>
                    </div>
                </div>

                <div class="space-y-3 mt-4">
                    @forelse($topTires as $index => $t)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-slate-300 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-blue-200">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $t->brand }} {{ $t->pattern }}</h4>
                                    <p class="text-[11px] text-slate-500">{{ $t->size_display }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-bold text-emerald-600 block">{{ $t->total_qty }} unit</span>
                                <span class="text-[10px] text-slate-400">Rp {{ number_format($t->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-slate-400 text-xs">
                            Belum ada data penjualan tercatat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Transactions & Warehouse Inbounds -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Transaksi Kasir Terbaru -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Transaksi Kasir Terkini</h3>
                        <p class="text-xs text-slate-500">Penjualan langsung lini depan</p>
                    </div>
                    <a href="{{ route('pos.history') }}" class="text-xs text-blue-600 hover:underline font-semibold">Lihat Semua &rarr;</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($recentTransactions as $tx)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-slate-300 transition">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $tx->invoice_number }}</span>
                                    @if($tx->isVoided())
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">VOID</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">SUKSES</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-700 mt-1">
                                    {{ $tx->customer_name }} {{ $tx->vehicle_plate ? "• {$tx->vehicle_plate}" : '' }}
                                </p>
                                <span class="text-[10px] text-slate-400">Kasir: {{ $tx->user ? $tx->user->name : '-' }} • {{ $tx->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <div class="text-xs font-bold text-slate-900">{{ $tx->formatted_grand_total }}</div>
                                <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank"
                                   class="text-[10px] text-blue-600 hover:underline mt-1 inline-block font-medium">
                                    Cetak Struk
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">Belum ada transaksi.</div>
                    @endforelse
                </div>
            </div>

            <!-- Inbound Goods Terbaru (Gudang) -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Penerimaan Barang (Inbound)</h3>
                        <p class="text-xs text-slate-500">Pengiriman stok ban dari distributor</p>
                    </div>
                    <a href="{{ route('inbounds.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">Lihat Semua &rarr;</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($recentInbounds as $inb)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-slate-300 transition">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $inb->inbound_number }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-sky-50 text-sky-700 border border-sky-200">INBOUND</span>
                                </div>
                                <p class="text-xs text-slate-700 mt-1">
                                    Supplier: {{ $inb->supplier ? $inb->supplier->name : 'Supplier Umum' }}
                                </p>
                                <span class="text-[10px] text-slate-400">Surat Jalan: {{ $inb->invoice_or_delivery_note ?? '-' }} • {{ $inb->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <div class="text-xs font-bold text-slate-900">{{ $inb->items->sum('quantity') }} unit</div>
                                <a href="{{ route('inbounds.show', $inb->id) }}"
                                   class="text-[10px] text-blue-600 hover:underline mt-1 inline-block font-medium">
                                    Detail Inbound
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">Belum ada penerimaan ban.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>