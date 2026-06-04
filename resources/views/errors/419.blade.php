<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 — Sesi Kedaluwarsa</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6"
             style="background: linear-gradient(135deg, #fef3c7, #fde68a)">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-2">419</h1>
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Sesi Kedaluwarsa</h2>
        <p class="text-sm text-gray-500 mb-8 leading-relaxed">
            Sesi halaman ini telah kedaluwarsa.<br>
            Muat ulang halaman dan coba lagi.
        </p>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('login') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white
                      border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Muat Ulang
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl"
               style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                Login Ulang
            </a>
        </div>
    </div>
</body>
</html>
