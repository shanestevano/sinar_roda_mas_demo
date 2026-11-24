<x-app-layout title="Riwayat Transaksi & Pembatalan (Void)">
    <div x-data="historyApp()" class="space-y-6">
        
        <!-- Header & Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Riwayat Transaksi Penjualan</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar seluruh nota transaksi kasir dan pembatalan (Void) dengan PIN otorisasi.</p>
            </div>

            <form method="GET" action="{{ route('pos.history') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Faktur / Pelanggan / Plat..."
                       class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs placeholder-slate-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">

                <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Sukses (Completed)</option>
                    <option value="voided" {{ request('status') === 'voided' ? 'selected' : '' }}>Dibatalkan (Void)</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-xs rounded-xl text-xs transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">No Faktur & Waktu</th>
                            <th class="py-4 px-6">Kasir</th>
                            <th class="py-4 px-6">Pelanggan & Plat</th>
                            <th class="py-4 px-6">Ban Dibeli</th>
                            <th class="py-4 px-6 text-right">Grand Total</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <span class="font-mono font-bold text-blue-600 text-sm block">{{ $tx->invoice_number }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $tx->created_at->format('d/m/Y H:i:s') }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-900">{{ $tx->user ? $tx->user->name : '-' }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-slate-900">{{ $tx->customer_name }}</div>
                                    @if($tx->vehicle_plate)
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-slate-800 text-amber-300 font-mono text-[10px] mt-0.5">
                                            {{ $tx->vehicle_plate }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        @foreach($tx->items as $item)
                                            <div class="text-[11px]">
                                                <strong class="text-slate-900">{{ $item->quantity }}x</strong>
                                                {{ $item->tire ? $item->tire->brand . ' ' . $item->tire->size_display : 'Ban' }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="font-bold text-sm text-slate-900 block">{{ $tx->formatted_grand_total }}</span>
                                    <span class="text-[10px] uppercase font-semibold text-slate-500">{{ $tx->payment_method }}</span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($tx->isCompleted())
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                            VOID (Batal)
                                        </span>
                                        @if($tx->void_reason)
                                            <div class="text-[9px] text-rose-400 mt-1 max-w-[140px] mx-auto truncate" title="{{ $tx->void_reason }}">
                                                {{ $tx->void_reason }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank"
                                           class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 hover:text-white transition"
                                           title="Cetak Ulang Struk">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </a>

                                        @if($tx->isCompleted())
                                            <button type="button"
                                                    @click="openVoidModal({{ $tx->id }}, '{{ $tx->invoice_number }}', '{{ $tx->formatted_grand_total }}')"
                                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-[11px] font-bold transition">
                                                Void
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 text-xs">
                                    Tidak ada data transaksi yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $transactions->links() }}
            </div>
        </div>

        <!-- Modal Security PIN Void Transaction (PRD 4.4) -->
        <div x-show="voidModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5"
                 @click.away="voidModalOpen = false">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Batalkan Transaksi (Void)</h3>
                        <p class="text-xs text-slate-500">Otorisasi PIN Supervisor / Owner Diperlukan</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <div class="text-slate-500">No Faktur: <strong class="text-amber-400 font-mono" x-text="targetInvoice"></strong></div>
                    <div class="text-slate-500">Total Nominal: <strong class="text-slate-900" x-text="targetTotal"></strong></div>
                    <p class="text-[11px] text-slate-500 mt-2">
                        * Membatalkan transaksi ini akan secara otomatis mengembalikan jumlah stok ban ke sistem gudang.
                    </p>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            PIN Keamanan Otorisasi
                        </label>
                        <input type="password" x-model="voidPin" maxlength="10" placeholder="Default PIN: 123456"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-mono text-center tracking-widest text-lg focus:ring-2 focus:ring-rose-500">
                        <span class="text-[10px] text-slate-500 mt-1 block">Masukkan PIN Owner / Supervisor (Default: 123456)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan Pembatalan (Wajib)
                        </label>
                        <input type="text" x-model="voidReason" placeholder="Contoh: Salah input ukuran ban / Pelanggan ganti tipe..."
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" @click="submitVoid()"
                            :disabled="isSubmitting || voidPin.trim().length === 0 || voidReason.trim().length === 0"
                            class="flex-1 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 disabled:opacity-40 text-white font-bold text-xs uppercase tracking-wider transition">
                        <span x-text="isSubmitting ? 'Memproses Void...' : 'Konfirmasi Batalkan (Void)'"></span>
                    </button>
                    <button type="button" @click="voidModalOpen = false"
                            class="px-4 py-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                        Batal
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function historyApp() {
            return {
                voidModalOpen: false,
                targetId: null,
                targetInvoice: '',
                targetTotal: '',
                voidPin: '',
                voidReason: '',
                isSubmitting: false,

                openVoidModal(id, invoice, total) {
                    this.targetId = id;
                    this.targetInvoice = invoice;
                    this.targetTotal = total;
                    this.voidPin = '';
                    this.voidReason = '';
                    this.voidModalOpen = true;
                },

                async submitVoid() {
                    if (!this.targetId) return;
                    this.isSubmitting = true;

                    try {
                        const res = await fetch(`/pos/void/${this.targetId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                pin: this.voidPin,
                                reason: this.voidReason
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Gagal membatalkan transaksi.');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan: ' + e.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
