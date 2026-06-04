@extends('layouts.manager')

@section('title', 'Buat Tugas')
@section('page-title', 'Buat Tugas Baru')

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('manager.tasks.store') }}">
            @csrf

            {{-- Judul --}}
            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Judul Tugas <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="Masukkan judul tugas..."
                       class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border rounded-xl transition-colors
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300
                              {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @error('title')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Jelaskan detail tugas ini..."
                          class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                 hover:border-gray-300 transition-colors resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Grid: Due date + Prioritas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Tenggat</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"
                           min="{{ date('Y-m-d') }}"
                           class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                  hover:border-gray-300 transition-colors">
                    @error('due_date')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                   hover:border-gray-300 transition-colors">
                        <option value="medium" {{ old('priority','medium') === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high"   {{ old('priority') === 'high'            ? 'selected' : '' }}>Tinggi</option>
                        <option value="low"    {{ old('priority') === 'low'             ? 'selected' : '' }}>Rendah</option>
                    </select>
                </div>
            </div>

            {{-- Assignee --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Assign ke Karyawan
                    <span class="text-gray-400 font-normal">(opsional, bisa lebih dari satu)</span>
                </label>
                @if ($employees->isEmpty())
                    <p class="text-sm text-gray-400 italic">Belum ada karyawan terdaftar.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-1">
                        @foreach ($employees as $emp)
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-gray-200 cursor-pointer
                                      hover:border-blue-300 hover:bg-blue-50/40 transition-colors has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50">
                            <input type="checkbox" name="assignees[]" value="{{ $emp->id }}"
                                   {{ in_array($emp->id, old('assignees', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">
                                    {{ strtoupper(substr($emp->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ $emp->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $emp->email }}</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition-all
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                        style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Tugas
                </button>
                <a href="{{ route('manager.tasks.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
