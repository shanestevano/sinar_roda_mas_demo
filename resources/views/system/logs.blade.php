<x-app-layout title="System Audit & Error Logs">
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">System Audit & Log Error Laravel</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Pemantauan langsung catatan kejadian dan exception sistem dari file <code>storage/logs/laravel.log</code>.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-mono text-slate-500">
                    Ukuran File: <strong class="text-slate-900">{{ number_format($fileSize / 1024, 1) }} KB</strong>
                </span>

                <form method="POST" action="{{ route('system.logs.clear') }}" onsubmit="return confirm('Bersihkan seluruh log sistem?');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-700 hover:text-rose-400 font-bold text-xs border border-slate-200 transition">
                        Bersihkan File Log
                    </button>
                </form>
            </div>
        </div>

        <!-- Log Entries List -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-white/50">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Entri Log Terkini (Maksimal 100 Terakhir)</span>
                <span class="text-xs font-mono text-amber-400 font-bold">{{ count($logEntries) }} Baris Entri</span>
            </div>

            <div class="divide-y divide-slate-100/80 max-h-[70vh] overflow-y-auto p-2">
                @forelse($logEntries as $log)
                    <div class="p-4 hover:bg-slate-50/80 rounded-2xl transition space-y-1.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase font-mono
                                    {{ $log['level'] === 'ERROR' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                    {{ $log['level'] === 'WARNING' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ $log['level'] === 'INFO' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                    {{ !in_array($log['level'], ['ERROR', 'WARNING', 'INFO']) ? 'bg-slate-800 text-slate-700' : '' }}">
                                    {{ $log['level'] }}
                                </span>
                                <span class="text-xs font-mono font-bold text-slate-700">{{ $log['timestamp'] }}</span>
                            </div>
                            <span class="text-[10px] text-slate-500 uppercase font-mono">{{ $log['env'] }}</span>
                        </div>

                        <div class="text-xs text-slate-800 font-mono bg-white/50 p-3 rounded-xl border border-slate-200/80 overflow-x-auto whitespace-pre-wrap">
                            {{ $log['message'] }}
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center text-slate-500 text-xs">
                        Tidak ada pesan error atau log pada <code>storage/logs/laravel.log</code>. Sistem dalam keadaan prima.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
