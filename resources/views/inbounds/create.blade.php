<x-app-layout title="Catat Penerimaan Barang Masuk">
    <div x-data="inboundForm()" class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('inbounds.index') }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Inbound
                </a>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Form Penerimaan Barang Masuk (Inbound)</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('inbounds.store') }}" class="space-y-6">
            @csrf

            <!-- Header Info -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">
                    Informasi Surat Jalan & Distributor
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Supplier Ban *</label>
                        <select name="supplier_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No Surat Jalan / Faktur Supplier</label>
                        <input type="text" name="supplier_invoice_number" placeholder="Contoh: SJ-BS-2024-9988"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Diterima Fisik *</label>
                        <input type="date" name="received_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Penerimaan</label>
                    <input type="text" name="notes" placeholder="Contoh: Pengiriman batch 1 bulan November, kondisi baik"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Dynamic Items Table -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Rincian Ban yang Diterima
                        </h2>
                        <p class="text-[11px] text-slate-500">Pilih ban, jumlah qty masuk, harga modal baru, dan kode DOT produksi ban</p>
                    </div>
                    <button type="button" @click="addItemRow()"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs border border-slate-200 flex items-center gap-1.5 transition">
                        <span>+ Tambah Baris</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="py-2 px-3 w-1/3">Pilih Ban (SKU / Merk / Ukuran)</th>
                                <th class="py-2 px-3 w-28">Qty Masuk</th>
                                <th class="py-2 px-3 w-36">Harga Modal Satuan (Rp)</th>
                                <th class="py-2 px-3 w-28">Kode DOT</th>
                                <th class="py-2 px-3 text-right">Subtotal</th>
                                <th class="py-2 px-3 text-center w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/60">
                            <template x-for="(row, idx) in items" :key="idx">
                                <tr>
                                    <td class="py-3 px-3">
                                        <select :name="'items[' + idx + '][tire_id]'" x-model="row.tire_id" @change="onTireSelected(idx)" required
                                                class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs">
                                            <option value="">-- Pilih Ban --</option>
                                            @foreach($tires as $t)
                                                <option value="{{ $t->id }}" data-cost="{{ (float)$t->cost_price }}" data-dot="{{ $t->dot_code }}">
                                                    {{ $t->brand }} {{ $t->pattern }} ({{ $t->size_display }}) - SKU: {{ $t->sku }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" :name="'items[' + idx + '][quantity]'" x-model.number="row.quantity" min="1" required
                                               class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs font-bold text-center">
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" :name="'items[' + idx + '][cost_price]'" x-model.number="row.cost_price" min="0" required
                                               class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs font-bold text-right">
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="text" :name="'items[' + idx + '][dot_code]'" x-model="row.dot_code" maxlength="4" placeholder="3224"
                                               class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs font-mono text-center">
                                    </td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900" x-text="formatRupiah(row.quantity * row.cost_price)">
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <button type="button" @click="removeItemRow(idx)" x-show="items.length > 1"
                                                class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Grand Total -->
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Nilai Pembelian Barang:</span>
                    <span class="text-lg font-black text-emerald-400" x-text="formatRupiah(grandTotal)"></span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('inbounds.index') }}" class="px-5 py-3 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-blue-600/20">
                    Simpan & Tambah Stok Masuk
                </button>
            </div>
        </form>
    </div>

    <script>
        function inboundForm() {
            return {
                items: [
                    { tire_id: '', quantity: 10, cost_price: 0, dot_code: '' }
                ],

                addItemRow() {
                    this.items.push({ tire_id: '', quantity: 1, cost_price: 0, dot_code: '' });
                },

                removeItemRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                onTireSelected(index) {
                    const select = document.querySelector(`select[name="items[${index}][tire_id]"]`);
                    if (select) {
                        const opt = select.options[select.selectedIndex];
                        if (opt && opt.value) {
                            this.items[index].cost_price = parseFloat(opt.getAttribute('data-cost') || 0);
                            this.items[index].dot_code = opt.getAttribute('data-dot') || '';
                        }
                    }
                },

                get grandTotal() {
                    return this.items.reduce((sum, i) => sum + ((i.quantity || 0) * (i.cost_price || 0)), 0);
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                }
            };
        }
    </script>
</x-app-layout>
