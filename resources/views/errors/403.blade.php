<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6"
             style="background: linear-gradient(135deg, #fee2e2, #fecaca)">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-2">403</h1>
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Akses Ditolak</h2>
        <p class="text-sm text-gray-500 mb-8 leading-relaxed">
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Pastikan Anda login dengan akun yang sesuai.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white
                          border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl"
                   style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                    Ke Halaman Saya
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl"
                   style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                    Login
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
