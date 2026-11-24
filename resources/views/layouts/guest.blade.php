<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - Sinar Roda Mas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-100 via-slate-50 to-blue-50/40 text-slate-800 min-h-screen flex items-center justify-center p-4 antialiased selection:bg-blue-600 selection:text-white">
    <div class="w-full max-w-md py-8">
        <!-- Brand Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white font-black text-xl shadow-lg shadow-blue-500/25 mb-3">
                SRM
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">SINAR RODA MAS</h1>
            <p class="text-slate-500 text-xs mt-1">Sistem Otomasi Toko & Pergudangan Ban</p>
        </div>

        {{ $slot }}

        <!-- Footer Copyright -->
        <div class="text-center mt-6 text-xs text-slate-400">
            &copy; {{ date('Y') }} Sinar Roda Mas. Hak Cipta Dilindungi.
        </div>
    </div>
</body>
</html>