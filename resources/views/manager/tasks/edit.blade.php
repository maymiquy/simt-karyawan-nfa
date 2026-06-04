@extends('layouts.manager')

@section('title', 'Edit Tugas')
@section('page-title', 'Edit Tugas')

@section('content')

<div class="max-w-2xl">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-5">
        <a href="{{ route('manager.tasks.index') }}" class="hover:text-gray-600 transition-colors">Kelola Tugas</a>
        <span>/</span>
        <a href="{{ route('manager.tasks.show', $task->id) }}" class="hover:text-gray-600 transition-colors truncate max-w-xs">{{ $task->title }}</a>
        <span>/</span>
        <span class="text-gray-600 font-medium">Edit</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('manager.tasks.update', $task->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Judul Tugas <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required
                       class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border rounded-xl transition-colors
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300
                              {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @error('title') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                 hover:border-gray-300 transition-colors resize-none">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Tenggat</label>
                    <input type="date" id="due_date" name="due_date"
                           value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                           class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                  hover:border-gray-300 transition-colors">
                    @error('due_date') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                    <select id="priority" name="priority" required
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
                        <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high"   {{ old('priority', $task->priority) === 'high'   ? 'selected' : '' }}>Tinggi</option>
                        <option value="low"    {{ old('priority', $task->priority) === 'low'    ? 'selected' : '' }}>Rendah</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors sm:max-w-xs">
                    @foreach(['pending'=>'Pending','in_progress'=>'Sedang Proses','completed'=>'Selesai','overdue'=>'Terlambat','cancelled'=>'Dibatalkan'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $task->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl"
                        style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('manager.tasks.show', $task->id) }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
