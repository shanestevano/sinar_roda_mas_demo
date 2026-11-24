<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Sinar Roda Mas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen selection:bg-blue-600 selection:text-white"
      x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between flex-shrink-0 shadow-xl lg:shadow-none">
            
            <div class="flex-1 flex flex-col min-h-0">
                <!-- Brand Header -->
                <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-sm shadow-md shadow-blue-500/20">
                            SRM
                        </div>
                        <div>
                            <span class="font-extrabold text-sm tracking-tight text-white block">SINAR RODA MAS</span>
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 block font-semibold">Toko & Gudang Ban</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links with Collapsible Accordion -->
                <nav class="p-3 space-y-1.5 overflow-y-auto flex-1 text-xs"
                     x-data="{
                         openGroup: '{{ request()->routeIs('pos.*', 'shifts.*') ? 'sales' : (request()->routeIs('products.*', 'inbounds.*', 'racks.*', 'suppliers.*') ? 'inventory' : (request()->routeIs('reports.*') ? 'reports' : (request()->routeIs('users.*', 'settings.*', 'system.*') ? 'system' : ''))) }}',
                         toggle(group) {
                             this.openGroup = (this.openGroup === group) ? null : group;
                         }
                     }">

                    <!-- Direct: Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard Utama</span>
                    </a>

                    <!-- Group 1: Penjualan & Kasir -->
                    @if(auth()->user()->hasRole(['kasir', 'admin', 'owner']))
                    <div class="rounded-xl overflow-hidden">
                        <button type="button" @click="toggle('sales')"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('pos.*', 'shifts.*') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Penjualan (Kasir)</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                 :class="openGroup === 'sales' ? 'rotate-180 text-white' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openGroup === 'sales'" x-cloak class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('pos.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('pos.index') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Kasir POS (Smart Search)
                            </a>
                            <a href="{{ route('pos.history') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('pos.history') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Riwayat Transaksi & Void
                            </a>
                            <a href="{{ route('shifts.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('shifts.index') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Rekap Shift Kasir
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Group 2: Inventaris & Gudang -->
                    @if(auth()->user()->hasRole(['gudang', 'admin']))
                    <div class="rounded-xl overflow-hidden">
                        <button type="button" @click="toggle('inventory')"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('products.*', 'inbounds.*', 'racks.*', 'suppliers.*') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>Inventaris & Gudang</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                 :class="openGroup === 'inventory' ? 'rotate-180 text-white' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openGroup === 'inventory'" x-cloak class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('products.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('products.*') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Master Data Ban (SKU)
                            </a>
                            <a href="{{ route('inbounds.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('inbounds.*') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Penerimaan (Inbound)
                            </a>
                            <a href="{{ route('racks.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('racks.*') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Lokasi Rak Ban
                            </a>
                            <a href="{{ route('suppliers.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('suppliers.*') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Data Supplier Ban
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Group 3: Laporan & Analitik -->
                    @if(auth()->user()->hasRole(['owner', 'admin']))
                    <div class="rounded-xl overflow-hidden">
                        <button type="button" @click="toggle('reports')"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('reports.*') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span>Laporan & Analitik</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                 :class="openGroup === 'reports' ? 'rotate-180 text-white' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openGroup === 'reports'" x-cloak class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('reports.sales') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('reports.sales') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Laporan Penjualan & Laba
                            </a>
                            <a href="{{ route('reports.stock-card') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('reports.stock-card') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Kartu Pergerakan Stok
                            </a>
                            <a href="{{ route('reports.dead-stock') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('reports.dead-stock') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Analisis Dead Stock (DOT)
                            </a>
                            <a href="{{ route('reports.audit-trail') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('reports.audit-trail') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Audit Trail Aktivitas
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Group 4: Manajemen Sistem (Admin) -->
                    @if(auth()->user()->isAdmin())
                    <div class="rounded-xl overflow-hidden">
                        <button type="button" @click="toggle('system')"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('users.*', 'settings.*', 'system.*') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Manajemen Sistem</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                 :class="openGroup === 'system' ? 'rotate-180 text-white' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openGroup === 'system'" x-cloak class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('users.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('users.*') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Kelola Pengguna
                            </a>
                            <a href="{{ route('settings.index') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('settings.*') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Pengaturan Toko & PIN
                            </a>
                            <a href="{{ route('system.logs') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('system.logs') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Log Audit Sistem
                            </a>
                            <a href="{{ route('system.backup') }}"
                               class="block px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('system.backup') ? 'text-white bg-blue-600 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                Cadangan Database
                            </a>
                        </div>
                    </div>
                    @endif
                </nav>
            </div>

            <!-- User Footer Profile -->
            <div class="p-3.5 border-t border-slate-800 bg-slate-900/90 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase
                                {{ auth()->user()->role === 'admin' ? 'bg-rose-500/20 text-rose-300' : '' }}
                                {{ auth()->user()->role === 'owner' ? 'bg-purple-500/20 text-purple-300' : '' }}
                                {{ auth()->user()->role === 'gudang' ? 'bg-sky-500/20 text-sky-300' : '' }}
                                {{ auth()->user()->role === 'kasir' ? 'bg-emerald-500/20 text-emerald-300' : '' }}">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-8 flex items-center justify-between z-30 flex-shrink-0 shadow-xs">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">{{ $title ?? 'Sinar Roda Mas' }}</h1>
                        <p class="text-[11px] text-slate-500 hidden sm:block">Aplikasi Operasional Penjualan & Pergudangan Ban</p>
                    </div>
                </div>

                <!-- Role Switcher & User Status -->
                <div class="flex items-center gap-3">
                    <!-- Quick Demo Role Switcher Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs text-slate-700 transition">
                            <span class="w-2 h-2 rounded-full
                                {{ auth()->user()->role === 'admin' ? 'bg-rose-500' : '' }}
                                {{ auth()->user()->role === 'owner' ? 'bg-purple-500' : '' }}
                                {{ auth()->user()->role === 'gudang' ? 'bg-sky-500' : '' }}
                                {{ auth()->user()->role === 'kasir' ? 'bg-emerald-500' : '' }}"></span>
                            <span class="font-semibold text-slate-800">Peran: {{ ucfirst(auth()->user()->role) }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50">
                            <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-100 mb-1">
                                Ganti Peran Demo (1-Click)
                            </div>
                            <a href="{{ route('quick-login', 'admin') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <div>
                                    <div class="font-bold text-slate-900">Admin IT</div>
                                    <div class="text-[10px] text-slate-500">Akses penuh sistem</div>
                                </div>
                            </a>
                            <a href="{{ route('quick-login', 'owner') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <div>
                                    <div class="font-bold text-slate-900">Owner</div>
                                    <div class="text-[10px] text-slate-500">Analitik & Laba Rugi</div>
                                </div>
                            </a>
                            <a href="{{ route('quick-login', 'gudang') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                <div>
                                    <div class="font-bold text-slate-900">Staf Gudang</div>
                                    <div class="text-[10px] text-slate-500">Stok, Rak & Masuk</div>
                                </div>
                            </a>
                            <a href="{{ route('quick-login', 'kasir') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <div>
                                    <div class="font-bold text-slate-900">Kasir POS</div>
                                    <div class="text-[10px] text-slate-500">Penjualan & Struk</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-3 shadow-xs">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-bold text-emerald-900">Berhasil</div>
                            <div>{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 shadow-xs">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-bold text-rose-900">Perhatian</div>
                            <div>{{ session('error') }}</div>
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-start gap-3 shadow-xs">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-bold text-blue-900">Informasi</div>
                            <div>{{ session('info') }}</div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                        <div class="font-bold flex items-center gap-2 mb-1 text-rose-900">
                            <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Terdapat input yang belum valid:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>