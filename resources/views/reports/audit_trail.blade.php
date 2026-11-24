<x-app-layout title="Audit Trail (Log Aktivitas)">
    <div class="space-y-6">
        
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Audit Trail & Log Aktivitas Sistem</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Catatan kronologis tindakan setiap pengguna: waktu, pelaku, alamat IP, aksi sistem, dan data perubahan.
                    </p>
                </div>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('reports.audit-trail') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 border-t border-slate-200 text-xs">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Aksi Sistem</label>
                    <select name="action" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pengguna (User)</label>
                    <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ strtoupper($u->role) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-xs rounded-xl transition">
                        Filter Log
                    </button>
                    @if(request()->hasAny(['action', 'user_id']))
                        <a href="{{ route('reports.audit-trail') }}" class="py-2 px-3 bg-slate-800 text-slate-500 hover:text-white rounded-xl">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Activity Logs Table -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Waktu Kejadian</th>
                            <th class="py-4 px-6">Pelaku (Pengguna)</th>
                            <th class="py-4 px-6">Jenis Aksi</th>
                            <th class="py-4 px-6">Uraian Aktivitas</th>
                            <th class="py-4 px-6">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    <span class="text-[10px] text-slate-500 block">({{ $log->created_at->diffForHumans() }})</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="font-bold text-slate-900">{{ $log->user->name }}</div>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold uppercase
                                            {{ $log->user->role === 'admin' ? 'bg-rose-500/20 text-rose-300' : '' }}
                                            {{ $log->user->role === 'owner' ? 'bg-purple-500/20 text-purple-300' : '' }}
                                            {{ $log->user->role === 'gudang' ? 'bg-blue-500/20 text-blue-300' : '' }}
                                            {{ $log->user->role === 'kasir' ? 'bg-emerald-500/20 text-emerald-300' : '' }}">
                                            {{ $log->user->role }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 italic">Sistem / Tamu</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full font-mono text-[10px] font-bold bg-slate-800 text-amber-300 border border-slate-200">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="text-slate-800 text-xs">{{ $log->description }}</div>
                                    @if($log->properties)
                                        <pre class="mt-1 p-2 rounded-xl bg-slate-50 border border-slate-200 font-mono text-[10px] text-slate-500 overflow-x-auto">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500 text-xs">
                                    Belum ada catatan log aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
