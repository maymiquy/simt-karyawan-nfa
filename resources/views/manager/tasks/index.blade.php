@extends('layouts.manager')

@section('title', 'Kelola Tugas')
@section('page-title', 'Kelola Tugas')

@section('header-actions')
<a href="{{ route('manager.tasks.create') }}"
   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white rounded-xl transition-all"
   style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    <span class="hidden sm:inline">Buat Tugas</span>
</a>
@endsection

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
    <form method="GET" action="{{ route('manager.tasks.index') }}" class="flex flex-col sm:flex-row gap-3 flex-wrap">
        <div class="relative flex-1 min-w-40">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul tugas..."
                   class="block w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
        </div>

        <select name="status"
                class="px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2
                       focus:ring-blue-500 focus:border-transparent hover:border-gray-300 text-gray-700 sm:w-40">
            <option value="">Semua Status</option>
            @foreach(['pending'=>'Pending','in_progress'=>'Proses','completed'=>'Selesai','overdue'=>'Terlambat','cancelled'=>'Dibatalkan'] as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="priority"
                class="px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2
                       focus:ring-blue-500 focus:border-transparent hover:border-gray-300 text-gray-700 sm:w-36">
            <option value="">Semua Prioritas</option>
            <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>Tinggi</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
            <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Rendah</option>
        </select>

        <button type="submit"
                class="px-4 py-2.5 text-sm font-medium text-white rounded-xl"
                style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
            Filter
        </button>

        @if (request()->hasAny(['search','status','priority','due_from','due_to']))
        <a href="{{ route('manager.tasks.index') }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-center">
            Reset
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
@if ($tasks->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 flex flex-col items-center text-center">
        <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
        </svg>
        <p class="text-gray-500 font-medium">Tidak ada tugas ditemukan</p>
        <p class="text-gray-400 text-sm mt-1">
            @if (request()->hasAny(['search','status','priority']))
                Coba ubah filter pencarian.
            @else
                Mulai dengan membuat tugas baru.
            @endif
        </p>
        <a href="{{ route('manager.tasks.create') }}"
           class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl"
           style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
            + Buat Tugas Baru
        </a>
    </div>
@else
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50/70 border-b border-gray-100">
                <tr>
                    <th class="text-left text-xs font-medium text-gray-500 px-5 py-3">Tugas</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3 hidden md:table-cell">Prioritas</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3 hidden sm:table-cell">Assignee</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3 hidden lg:table-cell">Tenggat</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3">Status</th>
                    <th class="text-right text-xs font-medium text-gray-500 px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($tasks as $task)
                @php
                    $isOverdue = $task->due_date && $task->due_date->isPast() && !in_array($task->status, ['completed','cancelled']);
                @endphp
                <tr class="hover:bg-gray-50/60 transition-colors {{ $isOverdue ? 'bg-red-50/40' : '' }}">
                    <td class="px-5 py-3.5">
                        <div class="flex items-start gap-2">
                            @if ($task->priority === 'high')
                                <span class="mt-1 w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                            @elseif ($task->priority === 'medium')
                                <span class="mt-1 w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></span>
                            @else
                                <span class="mt-1 w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
                            @endif
                            <div>
                                <p class="font-medium text-gray-800">{{ Str::limit($task->title, 45) }}</p>
                                @if ($task->description)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($task->description, 60) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3.5 hidden md:table-cell">
                        <x-priority-badge :priority="$task->priority"/>
                    </td>
                    <td class="px-3 py-3.5 hidden sm:table-cell">
                        @if ($task->assignments->isEmpty())
                            <span class="text-xs text-gray-400">—</span>
                        @else
                            <div class="flex -space-x-1">
                                @foreach ($task->assignments->take(3) as $a)
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white ring-2 ring-white"
                                     style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)"
                                     title="{{ $a->user?->name }}">
                                    {{ strtoupper(substr($a->user?->name ?? '?', 0, 1)) }}
                                </div>
                                @endforeach
                                @if ($task->assignments->count() > 3)
                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500 ring-2 ring-white">
                                        +{{ $task->assignments->count() - 3 }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 hidden lg:table-cell">
                        @if ($task->due_date)
                            <span class="text-xs {{ $isOverdue ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                {{ $task->due_date->translatedFormat('d M Y') }}
                            </span>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5">
                        <x-status-badge :status="$task->status"/>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('manager.tasks.show', $task->id) }}"
                               class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                               title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('manager.tasks.edit', $task->id) }}"
                               class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('manager.tasks.destroy', $task->id) }}"
                                  x-data
                                  @submit.prevent="if(confirm('Hapus tugas ini?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($tasks->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endif

@endsection
