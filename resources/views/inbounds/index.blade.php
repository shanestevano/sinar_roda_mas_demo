<x-app-layout title="Penerimaan Barang Masuk (Inbound)">
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div>
                <h1 class="text-xl font-black text-white tracking-tight">Penerimaan Barang Masuk (Inbound)</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Pencatatan pasokan ban fisik dari supplier yang secara otomatis memutasi penambahan stok gudang.
                </p>
            </div>

            <a href="{{ route('inbounds.create') }}"
               class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Barang Masuk Baru</span>
            </a>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 border-b border-slate-800 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">No Referensi Inbound</th>
                            <th class="py-4 px-6">Tanggal Terima</th>
                            <th class="py-4 px-6">Supplier & No Surat Jalan</th>
                            <th class="py-4 px-6">Jumlah Ban</th>
                            <th class="py-4 px-6 text-right">Total Nilai Masuk</th>
                            <th class="py-4 px-6">Penerima (Staf Gudang)</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @forelse($inbounds as $inb)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-4 px-6">
                                    <span class="font-mono font-bold text-blue-400 text-sm block">{{ $inb->reference_number }}</span>
                                </td>
                                <td class="py-4 px-6 font-medium text-white">
                                    {{ $inb->received_date->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white">{{ $inb->supplier ? $inb->supplier->name : '-' }}</div>
                                    <span class="text-[10px] text-slate-400">No Faktur/SJ: {{ $inb->supplier_invoice_number ?: '-' }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-200 font-bold text-xs">
                                        {{ $inb->items->sum('quantity') }} unit ({{ $inb->items->count() }} item)
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-black text-emerald-400 text-sm">
                                    {{ $inb->formatted_total_amount }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-300">{{ $inb->user ? $inb->user->name : '-' }}</span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('inbounds.show', $inb->id) }}"
                                       class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs transition inline-flex items-center gap-1 border border-slate-700">
                                        <span>Rincian</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 text-xs">
                                    Belum ada data penerimaan barang masuk tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-800">
                {{ $inbounds->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
