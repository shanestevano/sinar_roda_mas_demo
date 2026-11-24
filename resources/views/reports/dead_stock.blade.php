<x-app-layout title="Pencegahan Ban Kadaluarsa (Dead Stock)">
    <div class="space-y-6">
        
        <!-- Header & Explanation -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold mb-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Quality Assurance & Risk Control (DOT)
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Laporan Pengawasan Usia Produksi Ban (Kode DOT)</h1>
                    <p class="text-xs text-slate-500 mt-1 max-w-3xl">
                        Karet ban memiliki masa simpan optimal maksimal 3–5 tahun sejak diproduksi. Laporan ini mendeteksi ban dengan kode DOT lama untuk mencegah resiko ban mengeras, kadaluarsa, atau kerugian modal mati (*dead stock*).
                    </p>
                </div>

                <form method="GET" action="{{ route('reports.dead-stock') }}" class="flex items-center gap-2">
                    <label class="text-xs text-slate-500 font-semibold">Batas Usia Produksi:</label>
                    <select name="years" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs font-bold">
                        <option value="2" {{ $maxYears == 2 ? 'selected' : '' }}>&ge; 2 Tahun (Waspada)</option>
                        <option value="3" {{ $maxYears == 3 ? 'selected' : '' }}>&ge; 3 Tahun (Kritis / Dead Stock)</option>
                        <option value="4" {{ $maxYears == 4 ? 'selected' : '' }}>&ge; 4 Tahun (Kadaluarsa)</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Metric Alert Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-amber-500/30 rounded-3xl p-5 shadow-xl">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Model Ban Berumur &ge;{{ $maxYears }} Thn</span>
                <div class="text-2xl font-black text-amber-400">{{ $tires->count() }} SKU</div>
                <span class="text-[11px] text-slate-500 mt-1 block">Perlu prioritas display depan / promo</span>
            </div>

            <div class="bg-white border border-amber-500/30 rounded-3xl p-5 shadow-xl">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Unit Fisik Mengendap</span>
                <div class="text-2xl font-bold text-slate-900">{{ $totalAgedUnits }} unit</div>
                <span class="text-[11px] text-slate-500 mt-1 block">Stok fisik yang masih tersimpan di gudang</span>
            </div>

            <div class="bg-white border border-amber-500/30 rounded-3xl p-5 shadow-xl">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Estimasi Modal Tertahan (HPP)</span>
                <div class="text-2xl font-black text-rose-400">
                    Rp {{ number_format($totalAgedAssetValue, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-rose-300 mt-1 block">Aset modal yang berisiko jika tidak terjual</span>
            </div>
        </div>

        <!-- Aged Tires Table -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">SKU Ban</th>
                            <th class="py-4 px-6">Merk & Model Ban</th>
                            <th class="py-4 px-6">Ukuran Ring</th>
                            <th class="py-4 px-6 text-center">Kode DOT</th>
                            <th class="py-4 px-6 text-center">Tahun Produksi</th>
                            <th class="py-4 px-6 text-center">Estimasi Usia</th>
                            <th class="py-4 px-6 text-center">Sisa Stok</th>
                            <th class="py-4 px-6 text-right">Nilai Modal (HPP)</th>
                            <th class="py-4 px-6 text-center">Rekomendasi Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($tires as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6 font-mono font-bold text-blue-600">
                                    {{ $t->sku }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900 text-xs">{{ $t->brand }} {{ $t->pattern }}</div>
                                    <span class="text-[10px] text-slate-500">Lokasi: {{ $t->rackLocation ? $t->rackLocation->code : 'Buffer' }}</span>
                                </td>
                                <td class="py-4 px-6 font-mono font-semibold text-slate-800">
                                    {{ $t->size_display }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-900 text-sm">
                                    {{ $t->dot_code ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-slate-700">
                                    {{ $t->dot_year ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $t->dot_age_years >= 3 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $t->dot_age_years }} Tahun
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-slate-900 text-sm">
                                    {{ $t->stock }} unit
                                </td>
                                <td class="py-4 px-6 text-right font-black text-rose-400">
                                    Rp {{ number_format($t->stock * (float)$t->cost_price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($t->dot_age_years >= 3)
                                        <span class="px-2.5 py-1 rounded-xl bg-rose-500/20 text-rose-300 font-bold text-[10px] border border-rose-500/40">
                                            Clearance Sale / Diskon 20%
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-xl bg-amber-500/20 text-amber-300 font-bold text-[10px] border border-amber-500/40">
                                            Prioritaskan Penjualan Depan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-emerald-400 text-xs">
                                    ✅ Bagus! Seluruh stok ban di gudang masih dalam usia produksi prima (< {{ $maxYears }} tahun).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
