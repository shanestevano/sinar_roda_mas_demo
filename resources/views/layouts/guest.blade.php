<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - Sinar Roda Mas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased selection:bg-amber-500 selection:text-white">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-400 text-slate-950 font-black text-2xl shadow-xl shadow-amber-500/20 mb-4 border border-amber-300/40">
                SRM
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">SINAR RODA MAS</h1>
            <p class="text-slate-400 text-sm mt-1">Sistem Otomasi Toko & Gudang Ban Terintegrasi</p>
        </div>

        {{ $slot }}
    </div>
</body>
</html>
