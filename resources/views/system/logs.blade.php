<x-app-layout title="System Audit & Error Logs">
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div>
                <h1 class="text-xl font-black text-white tracking-tight">System Audit & Log Error Laravel</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Pemantauan langsung catatan kejadian dan exception sistem dari file <code>storage/logs/laravel.log</code>.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-mono text-slate-400">
                    Ukuran File: <strong class="text-white">{{ number_format($fileSize / 1024, 1) }} KB</strong>
                </span>

                <form method="POST" action="{{ route('system.logs.clear') }}" onsubmit="return confirm('Bersihkan seluruh log sistem?');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-300 hover:text-rose-400 font-bold text-xs border border-slate-700 transition">
                        Bersihkan File Log
                    </button>
                </form>
            </div>
        </div>

        <!-- Log Entries List -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/80">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Entri Log Terkini (Maksimal 100 Terakhir)</span>
                <span class="text-xs font-mono text-amber-400 font-bold">{{ count($logEntries) }} Baris Entri</span>
            </div>

            <div class="divide-y divide-slate-800/80 max-h-[70vh] overflow-y-auto p-2">
                @forelse($logEntries as $log)
                    <div class="p-4 hover:bg-slate-800/40 rounded-2xl transition space-y-1.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase font-mono
                                    {{ $log['level'] === 'ERROR' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : '' }}
                                    {{ $log['level'] === 'WARNING' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : '' }}
                                    {{ $log['level'] === 'INFO' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : '' }}
                                    {{ !in_array($log['level'], ['ERROR', 'WARNING', 'INFO']) ? 'bg-slate-800 text-slate-300' : '' }}">
                                    {{ $log['level'] }}
                                </span>
                                <span class="text-xs font-mono font-bold text-slate-300">{{ $log['timestamp'] }}</span>
                            </div>
                            <span class="text-[10px] text-slate-500 uppercase font-mono">{{ $log['env'] }}</span>
                        </div>

                        <div class="text-xs text-slate-200 font-mono bg-slate-950/80 p-3 rounded-xl border border-slate-800/80 overflow-x-auto whitespace-pre-wrap">
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
