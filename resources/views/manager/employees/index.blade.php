@extends('layouts.manager')

@section('title', 'Kelola Karyawan')
@section('page-title', 'Kelola Karyawan')

@section('header-actions')
<button onclick="document.getElementById('addEmployeeModal').classList.remove('hidden')"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white rounded-xl"
        style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
    </svg>
    <span class="hidden sm:inline">Tambah Karyawan</span>
</button>
@endsection

@section('content')

{{-- Employee list --}}
@if ($employees->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 flex flex-col items-center text-center">
        <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="text-gray-500 font-medium">Belum ada karyawan terdaftar</p>
        <button onclick="document.getElementById('addEmployeeModal').classList.remove('hidden')"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl"
                style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
            + Tambah Karyawan
        </button>
    </div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($employees as $emp)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start gap-3">
            <div class="w-11 h-11 rounded-full flex items-center justify-center text-base font-bold text-white flex-shrink-0"
                 style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">
                {{ strtoupper(substr($emp->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $emp->name }}</p>
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                        Employee
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $emp->email }}</p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                </svg>
                <span><strong class="text-gray-800">{{ $emp->active_tasks_count }}</strong> tugas aktif</span>
            </div>
            <span class="text-xs text-gray-400">
                Bergabung {{ $emp->created_at->translatedFormat('M Y') }}
            </span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ===== MODAL: Tambah Karyawan ===== --}}
<div id="addEmployeeModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     x-data="{ open: false }"
     @keydown.escape.window="document.getElementById('addEmployeeModal').classList.add('hidden')">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50"
         onclick="document.getElementById('addEmployeeModal').classList.add('hidden')"></div>

    {{-- Modal box --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900">Tambah Akun Karyawan</h3>
            <button onclick="document.getElementById('addEmployeeModal').classList.add('hidden')"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('manager.employees.store') }}" x-data="{ showPwd: false, showPwdConf: false }">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Nama karyawan..."
                       class="block w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors
                              {{ $errors->has('name') ? 'border-red-400' : '' }}">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="email@example.com"
                       class="block w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors
                              {{ $errors->has('email') ? 'border-red-400' : '' }}">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showPwd ? 'text' : 'password'" name="password" required minlength="8"
                           placeholder="Min. 8 karakter"
                           class="block w-full px-3.5 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
                    <button type="button" @click="showPwd = !showPwd"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" tabindex="-1">
                        <svg x-show="!showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Konfirmasi Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showPwdConf ? 'text' : 'password'" name="password_confirmation" required
                           placeholder="Ulangi password..."
                           class="block w-full px-3.5 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
                    <button type="button" @click="showPwdConf = !showPwdConf"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" tabindex="-1">
                        <svg x-show="!showPwdConf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPwdConf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl"
                        style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                    Buat Akun
                </button>
                <button type="button"
                        onclick="document.getElementById('addEmployeeModal').classList.add('hidden')"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Auto-open modal if there are validation errors --}}
@if ($errors->any())
<script>
    document.getElementById('addEmployeeModal').classList.remove('hidden');
</script>
@endif

@endsection
