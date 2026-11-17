<x-app-layout title="Kartu Pergerakan Stok Ban (Stock Card)">
    <div class="space-y-6">
        
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-black text-white tracking-tight">Kartu Pergerakan Stok Ban (Stock Card)</h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Audit trail otomatis setiap mutasi fisik: pasokan masuk, transaksi kasir, retur void, dan opname.
                    </p>
                </div>

                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
                   class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Ekspor Kartu Stok CSV</span>
                </a>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('reports.stock-card') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-4 border-t border-slate-800 text-xs">
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Ban (SKU / Merk)</label>
                    <select name="tire_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Jenis Ban --</option>
                        @foreach($tires as $t)
                            <option value="{{ $t->id }}" {{ $tireId == $t->id ? 'selected' : '' }}>
                                {{ $t->brand }} {{ $t->pattern }} ({{ $t->size_display }}) - SKU: {{ $t->sku }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Mutasi</label>
                    <select name="type" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white">
                        <option value="">Semua Mutasi</option>
                        <option value="inbound" {{ $type === 'inbound' ? 'selected' : '' }}>Barang Masuk (Inbound)</option>
                        <option value="sale" {{ $type === 'sale' ? 'selected' : '' }}>Penjualan Kasir (POS)</option>
                        <option value="void_return" {{ $type === 'void_return' ? 'selected' : '' }}>Retur Void Transaksi</option>
                        <option value="adjustment" {{ $type === 'adjustment' ? 'selected' : '' }}>Penyesuaian Opname</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rentang Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl transition">
                        Filter
                    </button>
                    <a href="{{ route('reports.stock-card') }}" class="py-2 px-3 bg-slate-800 text-slate-400 hover:text-white rounded-xl">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stock Mutation Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 border-b border-slate-800 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Waktu Mutasi</th>
                            <th class="py-4 px-6">Data Ban</th>
                            <th class="py-4 px-6">Jenis Mutasi</th>
                            <th class="py-4 px-6 text-center">Qty Mutasi</th>
                            <th class="py-4 px-6 text-center">Stok Awal</th>
                            <th class="py-4 px-6 text-center">Stok Akhir</th>
                            <th class="py-4 px-6">Petugas & Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @forelse($mutations as $m)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-4 px-6 font-mono text-slate-400 text-[11px]">
                                    {{ $m->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white text-xs">
                                        {{ $m->tire ? $m->tire->brand . ' ' . $m->tire->pattern : '-' }}
                                    </div>
                                    <span class="text-[10px] font-mono text-amber-400">{{ $m->tire ? $m->tire->size_display : '-' }} (SKU: {{ $m->tire ? $m->tire->sku : '-' }})</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $m->type_badge_color }}">
                                        {{ $m->type_name }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-black text-sm {{ $m->quantity > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                                </td>
                                <td class="py-4 px-6 text-center font-medium text-slate-400">
                                    {{ $m->stock_before }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-white">
                                    {{ $m->stock_after }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-white font-semibold block">{{ $m->user ? $m->user->name : 'Sistem' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $m->notes ?: '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 text-xs">
                                    Belum ada data riwayat pergerakan stok ban pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-800">
                {{ $mutations->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
