<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — {{ config('app.name', 'NF Academy') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased">

<div class="min-h-screen flex">

    {{-- ===== PANEL KIRI — Branding ===== --}}
    <div class="hidden lg:flex lg:w-2/5 xl:w-1/2 flex-col relative overflow-hidden"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1d4ed8 100%)">

        {{-- Decorative circles --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-10"
             style="background: radial-gradient(circle, #60a5fa, transparent)"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full opacity-10"
             style="background: radial-gradient(circle, #818cf8, transparent)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full opacity-5"
             style="background: radial-gradient(circle, #e0e7ff, transparent)"></div>

        {{-- Content --}}
        <div class="relative z-10 flex flex-col h-full p-10">
            {{-- Logo --}}
            <div class="flex items-center gap-3 mb-auto">
                <img src="{{ asset('images/logo-nf.png') }}"
                     alt="NF Academy"
                     class="h-10 w-auto object-contain"
                     onerror="this.style.display='none'">
                <span class="text-white font-semibold text-lg">NF Academy</span>
            </div>

            {{-- Center content --}}
            <div class="flex flex-col items-start py-16">
                {{-- Icon --}}
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-8"
                     style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px)">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>

                <h1 class="text-3xl xl:text-4xl font-bold text-white leading-tight mb-4">
                    Sistem Informasi<br>Manajemen Tugas
                </h1>
                <p class="text-blue-200 text-base leading-relaxed max-w-xs">
                    Kelola tugas karyawan dengan mudah, pantau progres secara real-time, dan tingkatkan produktivitas tim Anda.
                </p>

                {{-- Feature list --}}
                <ul class="mt-8 space-y-3">
                    @foreach(['Pembuatan & pembagian tugas', 'Pembaruan status real-time', 'Laporan & monitoring progress'] as $item)
                    <li class="flex items-center gap-3 text-blue-100 text-sm">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                              style="background: rgba(96,165,250,0.3)">
                            <svg class="w-3 h-3 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Footer --}}
            <p class="text-blue-300 text-xs mt-auto">
                &copy; {{ date('Y') }} NF Academy. All rights reserved.
            </p>
        </div>
    </div>

    {{-- ===== PANEL KANAN — Form Login ===== --}}
    <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10 bg-white">

        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center gap-2 mb-8">
            <img src="{{ asset('images/logo-nf.png') }}"
                 alt="NF Academy"
                 class="h-8 w-auto"
                 onerror="this.style.display='none'">
            <span class="font-semibold text-gray-800">NF Academy</span>
        </div>

        <div class="w-full max-w-md">

            {{-- Heading --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Selamat datang 👋</h2>
                <p class="text-gray-500 text-sm mt-1">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            {{-- Error alert --}}
            @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-700 text-sm">{{ $errors->first('email') }}</p>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
                @csrf

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="nama@email.com"
                            class="block w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 bg-gray-50 border rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            id="password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan password"
                            class="block w-full pl-10 pr-12 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl transition-colors hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        {{-- Toggle show/hide password --}}
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                            tabindex="-1"
                        >
                            <svg x-show="!showPassword" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center mb-6">
                    <input id="remember" name="remember" type="checkbox"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">
                        Ingat saya
                    </label>
                </div>

                {{-- Submit button --}}
                <button
                    type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-[0.98]"
                    style="background: linear-gradient(135deg, #1d4ed8, #2563eb); box-shadow: 0 4px 12px rgba(37,99,235,0.35)"
                    onmouseover="this.style.background='linear-gradient(135deg, #1e40af, #1d4ed8)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #1d4ed8, #2563eb)'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk
                </button>
            </form>

        </div>

        {{-- Version info --}}
        <p class="mt-10 text-xs text-gray-400 lg:hidden">
            &copy; {{ date('Y') }} NF Academy
        </p>
    </div>

</div>

</body>
</html>
