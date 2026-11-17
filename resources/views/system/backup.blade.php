<x-app-layout title="Pencadangan Database (Backup)">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <h1 class="text-xl font-black text-white tracking-tight">Pencadangan Database (Backup)</h1>
            <p class="text-xs text-slate-400 mt-1">
                Lakukan pencadangan (*snapshot*) seluruh data master ban, transaksi kasir, mutasi stok, dan log sistem secara berkala.
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16m-8 4v.01"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Database Utama Sinar Roda Mas</h3>
                    <div class="text-xs text-slate-400 mt-0.5">
                        Koneksi Aktif: <strong class="text-amber-400 uppercase font-mono">{{ $dbConnection }}</strong>
                        @if($hasSqlite)
                            • Ukuran: <strong class="text-slate-200 font-mono">{{ number_format($sqliteSize / 1024, 1) }} KB</strong>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-3 text-xs text-slate-300">
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Prosedur Backup & Recovery:</h4>
                <ul class="list-disc list-inside space-y-1 text-slate-400 text-xs">
                    <li>Salinan cadangan mencakup seluruh tabel transaksi kasir, master ban, penerimaan gudang, dan mutasi stok.</li>
                    <li>File cadangan dapat diunduh langsung ke komputer administrator atau server penyimpanan cloud.</li>
                    <li>Untuk lingkungan server produksi, perintah pencadangan terjadwal dapat diintegrasikan dengan <code>php artisan backup:run</code>.</li>
                </ul>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                <span class="text-[11px] text-slate-500">Format Snapshot: .sqlite / .sql</span>
                <a href="{{ route('system.backup.download') }}"
                   class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Cadangan Database Sekarang</span>
                </a>
            </div>
        </div>

    </div>
</x-app-layout>
