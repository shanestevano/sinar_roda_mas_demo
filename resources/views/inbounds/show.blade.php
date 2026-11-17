<x-app-layout title="Rincian Penerimaan Barang">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('inbounds.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Inbound
                </a>
                <h1 class="text-xl font-black text-white tracking-tight">Rincian Penerimaan Barang: {{ $inbound->reference_number }}</h1>
            </div>

            <button onclick="window.print()"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Bukti Penerimaan</span>
            </button>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            <!-- Meta Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">No Referensi:</span>
                    <strong class="text-blue-400 font-mono text-sm">{{ $inbound->reference_number }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Diterima:</span>
                    <strong class="text-white">{{ $inbound->received_date->format('d F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Supplier:</span>
                    <strong class="text-white">{{ $inbound->supplier ? $inbound->supplier->name : '-' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Penerima Gudang:</span>
                    <strong class="text-white">{{ $inbound->user ? $inbound->user->name : '-' }}</strong>
                </div>
            </div>

            @if($inbound->supplier_invoice_number || $inbound->notes)
            <div class="text-xs text-slate-400 space-y-1">
                @if($inbound->supplier_invoice_number)
                    <div>No Surat Jalan Supplier: <strong class="text-slate-200">{{ $inbound->supplier_invoice_number }}</strong></div>
                @endif
                @if($inbound->notes)
                    <div>Catatan: <span class="text-slate-300">{{ $inbound->notes }}</span></div>
                @endif
            </div>
            @endif

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950 border-b border-slate-800 text-[10px] uppercase font-bold text-slate-400">
                        <tr>
                            <th class="py-3 px-4">SKU Ban</th>
                            <th class="py-3 px-4">Merk & Tipe Ban</th>
                            <th class="py-3 px-4">Ukuran Ban</th>
                            <th class="py-3 px-4">Lokasi Rak</th>
                            <th class="py-3 px-4">Kode DOT</th>
                            <th class="py-3 px-4 text-center">Jumlah Masuk</th>
                            <th class="py-3 px-4 text-right">Harga Modal</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach($inbound->items as $item)
                            <tr>
                                <td class="py-3 px-4 font-mono font-bold text-amber-400">
                                    {{ $item->tire ? $item->tire->sku : '-' }}
                                </td>
                                <td class="py-3 px-4 font-bold text-white">
                                    {{ $item->tire ? $item->tire->brand . ' ' . $item->tire->pattern : 'Ban' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-200">
                                    {{ $item->tire ? $item->tire->size_display : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-400">
                                    {{ ($item->tire && $item->tire->rackLocation) ? $item->tire->rackLocation->code : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">
                                    {{ $item->dot_code ?: ($item->tire ? $item->tire->dot_code : '-') }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-400 text-sm">
                                    +{{ $item->quantity }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    Rp {{ number_format($item->cost_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-black text-white">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-slate-700 bg-slate-950 font-bold text-xs">
                        <tr>
                            <td colspan="5" class="py-3 px-4 text-right text-slate-400 uppercase">Total Qty & Nilai Pembelian:</td>
                            <td class="py-3 px-4 text-center text-emerald-400 text-sm font-black">{{ $inbound->items->sum('quantity') }} unit</td>
                            <td colspan="2" class="py-3 px-4 text-right text-emerald-400 text-base font-black">
                                {{ $inbound->formatted_total_amount }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
