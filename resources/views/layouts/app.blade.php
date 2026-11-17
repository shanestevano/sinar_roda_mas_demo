<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Sinar Roda Mas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen selection:bg-amber-500 selection:text-white"
      x-data="{ sidebarOpen: false, quickRoleOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between">
            
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black flex items-center justify-center text-sm shadow-md shadow-amber-500/20">
                            SRM
                        </div>
                        <div>
                            <span class="font-extrabold text-sm tracking-tight text-white block">SINAR RODA MAS</span>
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 block font-semibold">Toko & Gudang Ban</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-6 overflow-y-auto max-h-[calc(100vh-8rem)]">
                    <!-- General -->
                    <div>
                        <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Utama</div>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span>Dashboard {{ auth()->user()->role === 'owner' ? 'Eksekutif' : (auth()->user()->role === 'admin' ? 'Admin IT' : (auth()->user()->role === 'gudang' ? 'Gudang' : 'Kasir')) }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Kasir / POS Module -->
                    @if(auth()->user()->hasRole(['kasir', 'admin', 'owner']))
                    <div>
                        <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Penjualan (Kasir)</div>
                        <div class="space-y-1">
                            <a href="{{ route('pos.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('pos.index') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Kasir POS (Smart Search)</span>
                            </a>
                            <a href="{{ route('pos.history') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('pos.history') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span>Riwayat Transaksi & Void</span>
                            </a>
                            <a href="{{ route('shifts.index') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('shifts.index') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Rekap Shift Kasir</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Gudang / Inventaris Module -->
                    @if(auth()->user()->hasRole(['gudang', 'admin']))
                    <div>
                        <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Inventaris & Gudang</div>
                        <div class="space-y-1">
                            <a href="{{ route('products.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('products.*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>Master Data Ban (SKU)</span>
                            </a>
                            <a href="{{ route('inbounds.index') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('inbounds.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"/>
                                </svg>
                                <span>Penerimaan Barang (Inbound)</span>
                            </a>
                            <a href="{{ route('racks.index') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('racks.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span>Lokasi Rak Ban</span>
                            </a>
                            <a href="{{ route('suppliers.index') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Data Supplier Ban</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Laporan & Analitik (Owner & Admin) -->
                    @if(auth()->user()->hasRole(['owner', 'admin']))
                    <div>
                        <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Analitik & Laporan (Owner)</div>
                        <div class="space-y-1">
                            <a href="{{ route('reports.sales') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('reports.sales') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span>Laporan Penjualan & Laba</span>
                            </a>
                            <a href="{{ route('reports.stock-card') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('reports.stock-card') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                                <span>Kartu Pergerakan Stok</span>
                            </a>
                            <a href="{{ route('reports.dead-stock') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('reports.dead-stock') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Pencegahan Dead Stock (DOT)</span>
                            </a>
                            <a href="{{ route('reports.audit-trail') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('reports.audit-trail') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Audit Trail (Log Aktivitas)</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Manajemen Sistem (Admin) -->
                    @if(auth()->user()->isAdmin())
                    <div>
                        <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Manajemen Sistem (Admin)</div>
                        <div class="space-y-1">
                            <a href="{{ route('users.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('users.*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Manajemen Pengguna (CRUD)</span>
                            </a>
                            <a href="{{ route('settings.index') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Pengaturan Toko & PIN</span>
                            </a>
                            <a href="{{ route('system.logs') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('system.logs') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span>System Audit & Logs</span>
                            </a>
                            <a href="{{ route('system.backup') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('system.backup') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span>Cadangan Database</span>
                            </a>
                        </div>
                    </div>
                    @endif
                </nav>
            </div>

            <!-- User Footer Profile -->
            <div class="p-4 border-t border-slate-800 bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-amber-400 flex-shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold uppercase
                                {{ auth()->user()->role === 'admin' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : '' }}
                                {{ auth()->user()->role === 'owner' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : '' }}
                                {{ auth()->user()->role === 'gudang' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : '' }}
                                {{ auth()->user()->role === 'kasir' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : '' }}">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-slate-900 border-b border-slate-800 px-4 sm:px-8 flex items-center justify-between z-30 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-white tracking-tight">{{ $title ?? 'Sinar Roda Mas' }}</h2>
                        <p class="text-[11px] text-slate-400 hidden sm:block">Aplikasi Operasional Penjualan & Pergudangan Ban</p>
                    </div>
                </div>

                <!-- Role Switcher & User Status -->
                <div class="flex items-center gap-3">
                    <!-- Quick Demo Role Switcher Dropdown (Super handy for evaluation) -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700 text-xs text-slate-200 transition">
                            <span class="w-2 h-2 rounded-full
                                {{ auth()->user()->role === 'admin' ? 'bg-rose-500' : '' }}
                                {{ auth()->user()->role === 'owner' ? 'bg-purple-500' : '' }}
                                {{ auth()->user()->role === 'gudang' ? 'bg-blue-500' : '' }}
                                {{ auth()->user()->role === 'kasir' ? 'bg-emerald-500' : '' }}"></span>
                            <span class="font-semibold">Peran: {{ ucfirst(auth()->user()->role) }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-52 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl py-2 z-50">
                            <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-800 mb-1">
                                Ganti Peran Demo (1-Click)
                            </div>
                            <a href="{{ route('quick-login', 'admin') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <div>
                                    <div class="font-bold">Admin IT</div>
                                    <div class="text-[10px] text-slate-400">Akses penuh & manajemen</div>
                                </div>
                            </a>
                            <a href="{{ route('quick-login', 'owner') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <div>
                                    <div class="font-bold">Owner</div>
                                    <div class="text-[10px] text-slate-400">Analitik & Laba Rugi</div>
                                </div>
                            </a>
                            <a href="{{ route('quick-login', 'gudang') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <div>
                                    <div class="font-bold">Staf Gudang</div>
                                    <div class="text-[10px] text-slate-400">Stok, Rak & Masuk</div>
                                </div>
                            </a>
                            <a href="{{ route('quick-login', 'kasir') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <div>
                                    <div class="font-bold">Kasir POS</div>
                                    <div class="text-[10px] text-slate-400">Penjualan & Struk</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-950">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-start gap-3 shadow-lg">
                        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-bold">Berhasil!</div>
                            <div>{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-start gap-3 shadow-lg">
                        <svg class="w-5 h-5 text-rose-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-bold">Perhatian:</div>
                            <div>{{ session('error') }}</div>
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 rounded-2xl bg-blue-500/10 border border-blue-500/30 text-blue-300 text-sm flex items-start gap-3 shadow-lg">
                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-bold">Informasi:</div>
                            <div>{{ session('info') }}</div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm shadow-lg">
                        <div class="font-bold flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Terdapat input yang belum valid:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-200">
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
