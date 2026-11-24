<x-app-layout title="Rekap Shift Kasir">
    <div x-data="shiftApp()" class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Rekapitulasi Shift Kasir</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Pencatatan kas modal awal, total omset penjualan shift, dan rekonsiliasi kas fisik harian.
                </p>
            </div>

            <div>
                @if($activeShift)
                    <button type="button"
                            @click="openCloseModal({{ $activeShift->id }}, {{ (float)$activeShift->expected_cash }})"
                            class="px-5 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Tutup Shift Aktif</span>
                    </button>
                @else
                    <button type="button" @click="startModalOpen = true"
                            class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        <span>Buka Shift Baru</span>
                    </button>
                @endif
            </div>
        </div>

        @if($activeShift)
            <!-- Active Shift Card Indicator -->
            <div class="bg-gradient-to-r from-emerald-950/40 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 shadow-xl">
                <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs uppercase tracking-wider mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Shift Sedang Berjalan Aktif</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Waktu Mulai:</span>
                        <strong class="text-slate-900 text-sm">{{ $activeShift->opened_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Modal Awal Laci:</span>
                        <strong class="text-slate-900 text-sm">Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Penjualan Tunai:</span>
                        <strong class="text-emerald-400 text-sm font-black">Rp {{ number_format($activeShift->total_cash_sales, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Transaksi:</span>
                        <strong class="text-slate-900 text-sm">{{ $activeShift->total_transactions }} nota</strong>
                    </div>
                </div>
            </div>
        @endif

        <!-- Shifts History Table -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Tanggal & Kasir</th>
                            <th class="py-4 px-6">Jam Buka / Tutup</th>
                            <th class="py-4 px-6 text-right">Modal Awal</th>
                            <th class="py-4 px-6 text-right">Omset Tunai</th>
                            <th class="py-4 px-6 text-right">Kas Ekspektasi</th>
                            <th class="py-4 px-6 text-right">Kas Fisik Riil</th>
                            <th class="py-4 px-6 text-center">Selisih</th>
                            <th class="py-4 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($shifts as $s)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $s->user ? $s->user->name : '-' }}</div>
                                    <span class="text-[10px] text-slate-500">{{ $s->shift_date->format('d/m/Y') }}</span>
                                </td>
                                <td class="py-4 px-6 font-mono text-[11px] text-slate-500">
                                    {{ $s->opened_at->format('H:i') }} - {{ $s->closed_at ? $s->closed_at->format('H:i') : 'Aktif' }}
                                </td>
                                <td class="py-4 px-6 text-right font-medium text-slate-700">
                                    Rp {{ number_format($s->starting_cash, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-emerald-400">
                                    Rp {{ number_format($s->total_cash_sales, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-slate-900">
                                    Rp {{ number_format($s->expected_cash, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-slate-900">
                                    {{ $s->actual_cash !== null ? 'Rp ' . number_format($s->actual_cash, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($s->difference !== null)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black
                                            {{ $s->difference == 0 ? 'bg-emerald-500/20 text-emerald-300' : ($s->difference < 0 ? 'bg-rose-500/20 text-rose-300' : 'bg-blue-500/20 text-blue-300') }}">
                                            {{ $s->difference == 0 ? 'Pas (0)' : 'Rp ' . number_format($s->difference, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($s->isOpen())
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-800 text-slate-500">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500 text-xs">
                                    Belum ada data shift kasir tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $shifts->links() }}
            </div>
        </div>

        <!-- Open Shift Modal -->
        <div x-show="startModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 sm:p-8 max-w-sm w-full shadow-2xl space-y-4"
                 @click.away="startModalOpen = false">
                <h3 class="text-base font-bold text-slate-900">Buka Shift Kasir Baru</h3>
                <p class="text-xs text-slate-500">Masukkan jumlah uang modal awal di laci kasir.</p>

                <form method="POST" action="{{ route('shifts.start') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Modal Awal Tunai (Rp) *</label>
                        <input type="number" name="starting_cash" value="500000" required min="0"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-bold text-base text-right focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Shift</label>
                        <input type="text" name="notes" placeholder="Contoh: Shift Pagi Kasir Toko"
                               class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 py-3 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition">
                            Mulai Bertugas
                        </button>
                        <button type="button" @click="startModalOpen = false"
                                class="px-4 py-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Close Shift Modal -->
        <div x-show="closeModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 sm:p-8 max-w-sm w-full shadow-2xl space-y-4"
                 @click.away="closeModalOpen = false">
                <h3 class="text-base font-bold text-slate-900">Tutup Shift Kasir & Rekonsiliasi</h3>
                <p class="text-xs text-slate-500">Hitung total uang fisik di laci kasir untuk mengecek selisih.</p>

                <form :action="'/shifts/' + targetShiftId + '/close'" method="POST" class="space-y-4">
                    @csrf
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex justify-between">
                        <span class="text-slate-500">Ekspektasi Kas Laci:</span>
                        <strong class="text-slate-900" x-text="formatRupiah(targetExpected)"></strong>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Uang Kas Fisik Riil (Rp) *</label>
                        <input type="number" name="actual_cash" x-model.number="actualCash" required min="0"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-bold text-base text-right focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <div class="text-[11px] text-slate-500 mt-1 flex justify-between">
                            <span>Selisih:</span>
                            <span :class="(actualCash - targetExpected) < 0 ? 'text-rose-400 font-bold' : 'text-emerald-400 font-bold'"
                                  x-text="formatRupiah(actualCash - targetExpected)"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Penutupan</label>
                        <input type="text" name="notes" placeholder="Contoh: Kas fisik cocok tanpa selisih"
                               class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider transition">
                            Simpan & Tutup Shift
                        </button>
                        <button type="button" @click="closeModalOpen = false"
                                class="px-4 py-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function shiftApp() {
            return {
                startModalOpen: false,
                closeModalOpen: false,
                targetShiftId: null,
                targetExpected: 0,
                actualCash: 0,

                openCloseModal(id, expected) {
                    this.targetShiftId = id;
                    this.targetExpected = expected;
                    this.actualCash = expected;
                    this.closeModalOpen = true;
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                }
            };
        }
    </script>
</x-app-layout>
