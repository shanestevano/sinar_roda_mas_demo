<x-guest-layout>
    <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-sm">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm">
                <div class="font-bold flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Terjadi Kesalahan:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email Akun</label>
                <div class="relative">
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@sinarrodamas.com') }}" required autofocus
                        class="w-full px-4 py-3 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-sm"
                        placeholder="nama@sinarrodamas.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="password" name="password" value="password" required
                        class="w-full px-4 py-3 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-sm"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500/50">
                    <span>Ingat saya di perangkat ini</span>
                </label>
                <span class="text-slate-500">Default: password</span>
            </div>

            <button type="submit"
                class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 active:scale-[0.99] transition duration-150 flex items-center justify-center gap-2 text-sm">
                <span>Masuk ke Sistem</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>

        <!-- Quick Demo Switcher (Sesuai 4 Peran RBAC) -->
        <div class="mt-8 pt-6 border-t border-slate-700/60">
            <p class="text-xs font-semibold text-slate-400 text-center uppercase tracking-wider mb-3">
                Simulasi Akses Peran Pengguna (1-Click Login):
            </p>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('quick-login', 'admin') }}"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-700/40 hover:bg-slate-700 border border-slate-600/50 text-slate-200 text-xs font-medium transition hover:border-amber-500/50">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <div>
                        <div class="font-bold text-white">Admin IT</div>
                        <div class="text-[10px] text-slate-400">Sistem & Konfigurasi</div>
                    </div>
                </a>

                <a href="{{ route('quick-login', 'owner') }}"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-700/40 hover:bg-slate-700 border border-slate-600/50 text-slate-200 text-xs font-medium transition hover:border-amber-500/50">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    <div>
                        <div class="font-bold text-white">Owner</div>
                        <div class="text-[10px] text-slate-400">Dashboard & Finansial</div>
                    </div>
                </a>

                <a href="{{ route('quick-login', 'gudang') }}"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-700/40 hover:bg-slate-700 border border-slate-600/50 text-slate-200 text-xs font-medium transition hover:border-amber-500/50">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <div>
                        <div class="font-bold text-white">Staf Gudang</div>
                        <div class="text-[10px] text-slate-400">Master Ban & Masuk</div>
                    </div>
                </a>

                <a href="{{ route('quick-login', 'kasir') }}"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-700/40 hover:bg-slate-700 border border-slate-600/50 text-slate-200 text-xs font-medium transition hover:border-amber-500/50">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <div>
                        <div class="font-bold text-white">Kasir POS</div>
                        <div class="text-[10px] text-slate-400">Kasir & Struk Ban</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
