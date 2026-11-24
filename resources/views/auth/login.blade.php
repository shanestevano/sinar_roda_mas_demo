<x-guest-layout>
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <div class="font-bold flex items-center gap-2 mb-1 text-rose-900">
                    <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Terjadi Kesalahan:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Akun</label>
                <input type="email" id="email" name="email" value="{{ old('email', 'admin@sinarrodamas.com') }}" required autofocus
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition text-xs"
                    placeholder="nama@sinarrodamas.com">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <input type="password" id="password" name="password" value="password" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition text-xs"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span>Ingat saya di perangkat ini</span>
                </label>
                <span class="text-slate-400">Default: password</span>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/20 active:scale-[0.99] transition duration-150 flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                <span>Masuk ke Sistem</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>

        <!-- Quick Demo Switcher (4 Peran RBAC) -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <p class="text-[11px] font-semibold text-slate-400 text-center uppercase tracking-wider mb-3">
                Simulasi Akses Peran Pengguna (1-Click Login):
            </p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <a href="{{ route('quick-login', 'admin') }}"
                    class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-300 text-slate-700 transition">
                    <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Admin IT</div>
                        <div class="text-[10px] text-slate-400 truncate">Akses Penuh</div>
                    </div>
                </a>

                <a href="{{ route('quick-login', 'owner') }}"
                    class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-300 text-slate-700 transition">
                    <span class="w-2 h-2 rounded-full bg-purple-500 flex-shrink-0"></span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Owner</div>
                        <div class="text-[10px] text-slate-400 truncate">Laba Rugi</div>
                    </div>
                </a>

                <a href="{{ route('quick-login', 'gudang') }}"
                    class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-300 text-slate-700 transition">
                    <span class="w-2 h-2 rounded-full bg-sky-500 flex-shrink-0"></span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Staf Gudang</div>
                        <div class="text-[10px] text-slate-400 truncate">Stok & Rak</div>
                    </div>
                </a>

                <a href="{{ route('quick-login', 'kasir') }}"
                    class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-300 text-slate-700 transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Kasir POS</div>
                        <div class="text-[10px] text-slate-400 truncate">Kasir & Struk</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>