@extends('layouts.manager')

@section('title', 'Kelola Karyawan')
@section('page-title', 'Kelola Karyawan')

@section('header-actions')
<button onclick="document.getElementById('addEmployeeModal').classList.remove('hidden')"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white rounded-xl"
        style="background:linear-gradient(135deg,#1d4ed8,#2563eb)">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
    </svg>
    <span class="hidden sm:inline">Tambah Karyawan</span>
</button>
@endsection

@section('content')

@if ($employees->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
        <x-empty-state title="Belum ada karyawan terdaftar">
            <button onclick="document.getElementById('addEmployeeModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl"
                    style="background:linear-gradient(135deg,#1d4ed8,#2563eb)">+ Tambah Karyawan</button>
        </x-empty-state>
    </div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($employees as $emp)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition-all duration-200">
        <div class="flex items-start gap-3">
            <div class="w-11 h-11 rounded-full flex items-center justify-center text-base font-bold text-white shrink-0"
                 style="background:linear-gradient(135deg,#3b82f6,#1d4ed8)">
                {{ strtoupper(substr($emp->name,0,1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $emp->name }}</p>
                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                        Employee
                    </span>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ $emp->email }}</p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                </svg>
                <span><strong class="text-gray-800 dark:text-gray-200">{{ $emp->active_tasks_count }}</strong> tugas aktif</span>
            </div>
            <x-kpi-badge :percent="$emp->kpi_percent" />
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Modal Tambah Karyawan --}}
<div id="addEmployeeModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="document.getElementById('addEmployeeModal').classList.add('hidden')">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         onclick="document.getElementById('addEmployeeModal').classList.add('hidden')"></div>

    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 z-10 transition-colors duration-200">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tambah Akun Karyawan</h3>
            <button onclick="document.getElementById('addEmployeeModal').classList.add('hidden')"
                    class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('manager.employees.store') }}" x-data="{ showPwd: false, showPwdConf: false }">
            @csrf

            @foreach([['name','Nama Lengkap','text','Nama karyawan...'],['email','Email','email','email@example.com']] as [$n,$l,$t,$ph])
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ $l }} <span class="text-red-500">*</span></label>
                <input type="{{ $t }}" name="{{ $n }}" value="{{ old($n) }}" required placeholder="{{ $ph }}"
                       class="block w-full px-3.5 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl
                              placeholder-gray-400 dark:placeholder-gray-500
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors
                              {{ $errors->has($n)?'border-red-400':'' }}">
                @error($n) <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            @endforeach

            @foreach([['password','Password','showPwd','Min. 8 karakter'],['password_confirmation','Konfirmasi Password','showPwdConf','Ulangi password...']] as [$n,$l,$ref,$ph])
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ $l }} <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="{{ $ref }}?'text':'password'" name="{{ $n }}" required {{ $n==='password'?'minlength=8':'' }}
                           placeholder="{{ $ph }}"
                           class="block w-full px-3.5 pr-10 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl
                                  placeholder-gray-400 dark:placeholder-gray-500
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
                    <button type="button" @click="{{ $ref }} = !{{ $ref }}"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" tabindex="-1">
                        <svg x-show="!{{ $ref }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="{{ $ref }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error($n) <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            @endforeach

            <div class="flex gap-3 mt-5">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl"
                        style="background:linear-gradient(135deg,#1d4ed8,#2563eb)">Buat Akun</button>
                <button type="button"
                        onclick="document.getElementById('addEmployeeModal').classList.add('hidden')"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@if ($errors->any())
<script>document.getElementById('addEmployeeModal').classList.remove('hidden');</script>
@endif

@endsection
