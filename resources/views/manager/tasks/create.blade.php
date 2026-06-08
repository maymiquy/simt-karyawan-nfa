@extends('layouts.manager')

@section('title', 'Buat Tugas')
@section('page-title', 'Buat Tugas Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('manager.tasks.store') }}">
            @csrf

            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Judul Tugas <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="Masukkan judul tugas..."
                       class="block w-full px-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border rounded-xl transition-colors
                              placeholder-gray-400 dark:placeholder-gray-500
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500
                              {{ $errors->has('title') ? 'border-red-400' : 'border-gray-200 dark:border-gray-600' }}">
                @error('title') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Jelaskan detail tugas ini..."
                          class="block w-full px-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl
                                 placeholder-gray-400 dark:placeholder-gray-500
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal & Jam Tenggat</label>
                    <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date') }}" min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="block w-full px-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
                    @error('due_date') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
                        <option value="medium" {{ old('priority','medium')==='medium'?'selected':'' }}>Sedang</option>
                        <option value="high"   {{ old('priority')==='high'  ?'selected':'' }}>Tinggi</option>
                        <option value="low"    {{ old('priority')==='low'   ?'selected':'' }}>Rendah</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Assign ke Karyawan
                    <span class="text-gray-400 dark:text-gray-500 font-normal">(opsional)</span>
                </label>
                @if ($employees->isEmpty())
                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada karyawan terdaftar.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-52 overflow-y-auto p-1">
                        @foreach ($employees as $emp)
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 cursor-pointer
                                      hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50/40 dark:hover:bg-blue-900/20 transition-colors
                                      has-[:checked]:border-blue-400 dark:has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20">
                            <input type="checkbox" name="assignees[]" value="{{ $emp->id }}"
                                   {{ in_array($emp->id, old('assignees',[])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-500 rounded focus:ring-blue-500">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                                     style="background:linear-gradient(135deg,#3b82f6,#1d4ed8)">
                                    {{ strtoupper(substr($emp->name,0,1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $emp->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $emp->email }}</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl"
                        style="background:linear-gradient(135deg,#1d4ed8,#2563eb)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Tugas
                </button>
                <a href="{{ route('manager.tasks.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
