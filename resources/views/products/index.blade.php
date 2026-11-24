<x-app-layout title="Master Data Ban (Gudang)">
    <div x-data="productIndexApp()" class="space-y-6">
        
        <!-- Top Action & Search Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Katalog & Master Data Ban</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Kelola spesifikasi teknis, harga modal & jual, pelacakan kode DOT, dan lokasi rak ban.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('products.create') }}"
                   class="px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-xs text-xs uppercase tracking-wider shadow-lg shadow-blue-500/10 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Ban Baru</span>
                </a>
                <a href="{{ route('inbounds.create') }}"
                   class="px-4 py-2.5 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs uppercase tracking-wider border border-slate-300 shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Inbound Masuk</span>
                </a>
            </div>
        </div>

        <!-- Filter Badges & Search Form -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs space-y-4">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
                <div class="lg:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari SKU, Barcode, Merk, Seri, Ukuran, atau DOT..."
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs placeholder-slate-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <select name="brand" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs">
                        <option value="">Semua Merk</option>
                        @foreach($brands as $b)
                            <option value="{{ $b }}" {{ request('brand') === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="rim" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs">
                        <option value="">Semua Ukuran Ring</option>
                        @foreach($rims as $r)
                            <option value="{{ $r }}" {{ request('rim') == $r ? 'selected' : '' }}>Ring {{ $r }}"</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold rounded-xl text-xs transition border border-slate-200">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'brand', 'rim', 'filter']))
                        <a href="{{ route('products.index') }}" class="py-2.5 px-3 bg-slate-800 text-slate-500 hover:text-white rounded-xl text-xs" title="Reset">
                            ✕
                        </a>
                    @endif
                </div>
            </form>

            <!-- Quick Filter Pills -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-200/80 text-xs">
                <span class="text-slate-500 text-[11px] font-semibold">Filter Cepat:</span>
                <a href="{{ route('products.index') }}"
                   class="px-3 py-1 rounded-full text-xs font-medium transition {{ !request('filter') ? 'bg-blue-600 text-white font-semibold' : 'bg-slate-800 text-slate-500 hover:text-white' }}">
                    Semua Ban
                </a>
                <a href="{{ route('products.index', ['filter' => 'low_stock']) }}"
                   class="px-3 py-1 rounded-full text-xs font-medium transition flex items-center gap-1.5 {{ request('filter') === 'low_stock' ? 'bg-rose-500 text-white font-bold' : 'bg-slate-800 text-slate-500 hover:text-rose-400' }}">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    <span>Stok Kritis ({{ $lowStockCount }})</span>
                </a>
                <a href="{{ route('products.index', ['filter' => 'aged_dot']) }}"
                   class="px-3 py-1 rounded-full text-xs font-medium transition flex items-center gap-1.5 {{ request('filter') === 'aged_dot' ? 'bg-blue-600 text-white font-semibold' : 'bg-slate-800 text-slate-500 hover:text-amber-400' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Peringatan DOT Lama ({{ $agedDotCount }})</span>
                </a>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">SKU / Barcode</th>
                            <th class="py-4 px-6">Merk & Model Ban</th>
                            <th class="py-4 px-6">Ukuran & Tipe</th>
                            <th class="py-4 px-6">Kode DOT (Tahun)</th>
                            <th class="py-4 px-6 text-right">Harga Modal</th>
                            <th class="py-4 px-6 text-right">Harga Jual</th>
                            <th class="py-4 px-6 text-center">Stok & Rak</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($tires as $tire)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <span class="font-mono font-bold text-blue-600 text-xs block">{{ $tire->sku }}</span>
                                    <span class="font-mono text-[10px] text-slate-500">{{ $tire->barcode ?: '-' }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900 text-xs">{{ $tire->brand }} {{ $tire->pattern }}</div>
                                    @if($tire->notes)
                                        <div class="text-[10px] text-slate-500 max-w-xs truncate" title="{{ $tire->notes }}">{{ $tire->notes }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-800 text-slate-800 font-mono inline-block">
                                        {{ $tire->size_display }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">{{ $tire->type }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-slate-900">{{ $tire->dot_code ?: '-' }}</span>
                                        @if($tire->isAgedOrExpired(2))
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200"
                                                  title="Ban produksi tahun {{ $tire->dot_year }} (Berumur {{ $tire->dot_age_years }} tahun). Waspada dead stock!">
                                                &ge;{{ $tire->dot_age_years }} Thn
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-500">({{ $tire->dot_year }})</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right font-medium text-slate-500">
                                    {{ $tire->formatted_cost_price }}
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-slate-900">
                                    {{ $tire->formatted_selling_price }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black
                                        {{ $tire->isLowStock() ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        <span>{{ $tire->stock }}</span>
                                        <span class="text-[10px] font-normal">unit</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-mono">
                                        {{ $tire->rackLocation ? $tire->rackLocation->code : 'Tanpa Rak' }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Cetak Barcode -->
                                        <a href="{{ route('products.barcode', $tire->id) }}" target="_blank"
                                           class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 hover:text-amber-400 transition"
                                           title="Cetak Label Barcode Ban">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                        </a>

                                        <!-- Stock Opname / Penyesuaian -->
                                        <button type="button"
                                                @click="openAdjustModal({{ $tire->id }}, '{{ $tire->sku }}', '{{ $tire->brand }} {{ $tire->pattern }}', {{ $tire->stock }})"
                                                class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 hover:text-blue-400 transition"
                                                title="Penyesuaian Stok (Stock Opname)">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </button>

                                        <!-- Edit -->
                                        <a href="{{ route('products.edit', $tire->id) }}"
                                           class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 hover:text-white transition"
                                           title="Edit Ban">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <!-- Hapus / Nonaktifkan -->
                                        <form method="POST" action="{{ route('products.destroy', $tire->id) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan ban {{ $tire->sku }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 transition" title="Hapus/Arsipkan">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500 text-xs">
                                    Tidak ada data ban yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $tires->links() }}
            </div>
        </div>

        <!-- Stock Opname Modal (PRD 4.3) -->
        <div x-show="adjustModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5"
                 @click.away="adjustModalOpen = false">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-500/40 text-blue-400 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Penyesuaian Stok (Stock Opname)</h3>
                        <p class="text-xs text-slate-500" x-text="targetTireName"></p>
                    </div>
                </div>

                <form :action="'/products/' + targetTireId + '/adjust'" method="POST" class="space-y-4">
                    @csrf
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex justify-between">
                        <span class="text-slate-500">Stok Sistem Saat Ini:</span>
                        <span class="font-bold text-slate-900" x-text="currentStock + ' unit'"></span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Jumlah Stok Fisik Riil
                        </label>
                        <input type="number" name="actual_stock" x-model.number="actualStock" min="0" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-bold text-base focus:ring-2 focus:ring-blue-500 text-center">
                        <div class="text-[11px] text-slate-500 mt-1 flex justify-between">
                            <span>Selisih Mutasi:</span>
                            <span :class="(actualStock - currentStock) < 0 ? 'text-rose-400 font-bold' : 'text-emerald-400 font-bold'"
                                  x-text="(actualStock - currentStock > 0 ? '+' : '') + (actualStock - currentStock) + ' unit'"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan Penyesuaian (Audit / Rusak / Ditemukan)
                        </label>
                        <input type="text" name="reason" placeholder="Contoh: Hasil audit opname fisik akhir bulan" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider transition">
                            Simpan Perubahan Stok
                        </button>
                        <button type="button" @click="adjustModalOpen = false"
                                class="px-4 py-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function productIndexApp() {
            return {
                adjustModalOpen: false,
                targetTireId: null,
                targetTireName: '',
                currentStock: 0,
                actualStock: 0,

                openAdjustModal(id, sku, name, stock) {
                    this.targetTireId = id;
                    this.targetTireName = `${sku} - ${name}`;
                    this.currentStock = stock;
                    this.actualStock = stock;
                    this.adjustModalOpen = true;
                }
            };
        }
    </script>
</x-app-layout>
