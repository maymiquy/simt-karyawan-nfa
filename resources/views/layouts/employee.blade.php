<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'NF Academy') }}</title>
    {{-- Prevent dark mode flash --}}
    <script>
        (function(){
            var s=localStorage.getItem('darkMode');
            var p=window.matchMedia('(prefers-color-scheme: dark)').matches;
            if(s==='true'||(s===null&&p)) document.documentElement.classList.add('dark');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-950 font-sans antialiased transition-colors duration-200"
      x-data="{
          sidebarOpen: false,
          dark: localStorage.getItem('darkMode') === 'true',
          toggleDark() {
              this.dark = !this.dark;
              localStorage.setItem('darkMode', this.dark);
              document.documentElement.classList.toggle('dark', this.dark);
          }
      }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black/50 lg:hidden"
     style="display:none"></div>

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR (dark gradient works in both themes) ===== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
           style="background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%)">

        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <img src="{{ asset('images/logo-nf.png') }}" alt="NF Academy"
                 class="h-8 w-auto object-contain" onerror="this.style.display='none'">
            <div>
                <p class="text-white font-semibold text-sm leading-tight">NF Academy</p>
                <p class="text-blue-300 text-xs">Manajemen Tugas</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            <a href="{{ route('employee.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('employee.dashboard') ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('employee.tasks.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('employee.tasks.*') ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Tugas Saya
            </a>
            <a href="{{ route('employee.activity.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('employee.activity.*') ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Aktivitas Saya
            </a>
            <a href="{{ route('employee.kpi.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('employee.kpi.*') ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                KPI Saya
            </a>
            <a href="{{ route('employee.calendar.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('employee.calendar.*') ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Kalender Tenggat
            </a>
        </nav>

        <div class="px-3 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-3 py-2 mb-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                     style="background: rgba(96,165,250,0.4)">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-blue-300 text-xs truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-blue-200 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN ===== --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="shrink-0 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-4 sm:px-6 h-14 flex items-center justify-between shadow-sm transition-colors duration-200">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-gray-900 dark:text-white font-semibold text-sm sm:text-base">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2">
                {{-- Notifikasi (bell) --}}
                @php
                    $notifs = Auth::user()->unreadNotifications()->latest()->take(8)->get();
                    $notifCount = Auth::user()->unreadNotifications()->count();
                @endphp
                <div class="relative" x-data="{
                        open: false,
                        count: {{ $notifCount }},
                        async poll() {
                            try {
                                const r = await fetch('{{ route('employee.notifications.json') }}', {headers:{'Accept':'application/json'}});
                                const d = await r.json();
                                if (typeof d.unread === 'number') this.count = d.unread;
                            } catch (e) {}
                        }
                     }"
                     x-init="setInterval(() => poll(), 60000)"
                     @click.outside="open = false">
                    <button @click="open = !open"
                            class="relative p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="count > 0" x-cloak
                              class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"
                              x-text="count > 9 ? '9+' : count"></span>
                    </button>

                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 mt-2 w-80 max-w-[90vw] bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notifikasi</p>
                            @if($notifCount > 0)
                            <form method="POST" action="{{ route('employee.notifications.readAll') }}">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Tandai semua</button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($notifs as $n)
                            @php
                                $nt = $n->data['type'] ?? 'info';
                                $dot = ['assigned'=>'bg-blue-500','revision'=>'bg-amber-500','deadline'=>'bg-red-500'][$nt] ?? 'bg-gray-400';
                            @endphp
                            <a href="{{ route('employee.notifications.read', $n->id) }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-50 dark:border-gray-700/50">
                                <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $dot }}"></span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $n->data['title'] ?? 'Notifikasi' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $n->data['message'] ?? '' }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                            @empty
                            <p class="px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Tidak ada notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Dark mode toggle --}}
                <button @click="toggleDark()"
                        class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        :title="dark ? 'Mode Terang' : 'Mode Gelap'">
                    <svg x-show="!dark" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="dark" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                    Employee
                </span>
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                     style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-4 sm:px-6 py-6 page-fade">
            @yield('content')
        </main>
    </div>
</div>

<x-toast />

@stack('scripts')
</body>
</html>
