@extends('layouts.manager')

@section('title', $task->title)
@section('page-title', 'Detail Tugas')

@section('header-actions')
<a href="{{ route('manager.tasks.edit', $task->id) }}"
   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
    Edit
</a>
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-xs text-gray-400 mb-5">
    <a href="{{ route('manager.tasks.index') }}" class="hover:text-gray-600 transition-colors">Kelola Tugas</a>
    <span>/</span>
    <span class="text-gray-600 font-medium truncate max-w-xs">{{ $task->title }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ===== PANEL KIRI — Info Tugas ===== --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Header tugas --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <div class="flex flex-wrap gap-2 mb-3">
                <x-status-badge :status="$task->status"/>
                <x-priority-badge :priority="$task->priority"/>
                @if ($task->is_overdue)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                        Terlambat
                    </span>
                @endif
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $task->title }}</h2>
            @if ($task->description)
                <p class="text-sm text-gray-600 leading-relaxed">{{ $task->description }}</p>
            @else
                <p class="text-sm text-gray-400 italic">Tidak ada deskripsi.</p>
            @endif
        </div>

        {{-- Meta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs mb-0.5">Dibuat oleh</dt>
                    <dd class="font-medium text-gray-800">{{ $task->creator?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-0.5">Tanggal Dibuat</dt>
                    <dd class="font-medium text-gray-800">{{ $task->created_at->translatedFormat('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-0.5">Tenggat</dt>
                    <dd class="font-medium {{ $task->is_overdue ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $task->due_date?->translatedFormat('d M Y') ?? '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Assignment Tracker --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Assignee & Progres</h3>
                <span class="text-xs text-gray-400">{{ $task->assignments->count() }} karyawan</span>
            </div>

            @if ($task->assignments->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">Belum ada karyawan yang di-assign.</p>
            @else
                <div class="space-y-3">
                    @foreach ($task->assignments as $a)
                    @php
                        $progressMap = [
                            'not_started' => ['label' => 'Belum Mulai',   'class' => 'bg-gray-100 text-gray-600'],
                            'on_progress' => ['label' => 'Sedang Proses',  'class' => 'bg-blue-50 text-blue-700'],
                            'done'        => ['label' => 'Selesai',        'class' => 'bg-green-50 text-green-700'],
                            'revision'    => ['label' => 'Perlu Revisi',   'class' => 'bg-amber-50 text-amber-700'],
                        ];
                        $pb = $progressMap[$a->progress] ?? ['label' => $a->progress, 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <div class="border border-gray-100 rounded-xl p-4"
                         x-data="{ showReview: false, showReport: false }">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                                 style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">
                                {{ strtoupper(substr($a->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-800">{{ $a->user?->name ?? '—' }}</p>
                                    <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-md {{ $pb['class'] }}">
                                        {{ $pb['label'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $a->user?->email }}</p>
                                @if ($a->submitted_at)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Dikirim: {{ $a->submitted_at->translatedFormat('d M Y, H:i') }}
                                    </p>
                                @endif

                                {{-- Laporan employee --}}
                                @if ($a->completion_notes)
                                <div class="mt-2">
                                    <button @click="showReport = !showReport"
                                            class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                        <span x-text="showReport ? 'Sembunyikan laporan' : 'Lihat laporan'"></span>
                                        <svg class="w-3 h-3" :class="showReport ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div x-show="showReport" x-transition class="mt-2 p-3 rounded-lg bg-gray-50 text-xs text-gray-600 leading-relaxed"
                                         style="display:none">
                                        {{ $a->completion_notes }}
                                    </div>
                                </div>
                                @endif

                                {{-- Catatan manager --}}
                                @if ($a->manager_notes)
                                <div class="mt-2 p-2.5 rounded-lg bg-amber-50 text-xs text-amber-700">
                                    <span class="font-medium">Catatan:</span> {{ $a->manager_notes }}
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Review actions (tampil jika done dan belum di-review, atau on_progress) --}}
                        @if (in_array($a->progress, ['done', 'on_progress']))
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div x-show="!showReview" class="flex items-center gap-2">
                                @if ($a->progress === 'done')
                                <button @click="showReview = true"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-white rounded-lg"
                                        style="background: linear-gradient(135deg, #16a34a, #22c55e)">
                                    Setujui Laporan
                                </button>
                                <button @click="showReview = true"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-100 rounded-lg hover:bg-amber-100 transition-colors">
                                    Minta Revisi
                                </button>
                                @endif
                            </div>

                            <div x-show="showReview" x-transition style="display:none">
                                <form method="POST" action="{{ route('manager.assignments.review', $a->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" x-ref="reviewAction" value="approve">
                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                            Catatan untuk karyawan (opsional)
                                        </label>
                                        <textarea name="manager_notes" rows="3"
                                                  placeholder="Tuliskan feedback atau catatan revisi..."
                                                  class="block w-full px-3 py-2 text-xs text-gray-800 bg-white border border-gray-200 rounded-lg
                                                         focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ $a->manager_notes }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit"
                                                @click="$refs.reviewAction.value = 'approve'"
                                                class="flex-1 px-3 py-2 text-xs font-semibold text-white rounded-lg"
                                                style="background: linear-gradient(135deg, #16a34a, #22c55e)">
                                            Setujui
                                        </button>
                                        <button type="submit"
                                                @click="$refs.reviewAction.value = 'revision'"
                                                class="flex-1 px-3 py-2 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                            Minta Revisi
                                        </button>
                                        <button type="button" @click="showReview = false"
                                                class="px-3 py-2 text-xs text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        {{-- Remove assignment --}}
                        <form method="POST" action="{{ route('manager.assignments.destroy', $a->id) }}"
                              class="mt-2"
                              x-data
                              @submit.prevent="if(confirm('Hapus assignment ini?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-400 hover:text-red-600 hover:underline transition-colors">
                                Hapus assignment
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== PANEL KANAN — Aksi ===== --}}
    <div class="space-y-4">

        {{-- Assign karyawan baru --}}
        @if ($employees->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">+ Assign Karyawan</h3>
            <form method="POST" action="{{ route('manager.assignments.store', $task->id) }}">
                @csrf
                <select name="user_id" required
                        class="block w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3">
                    <option value="">Pilih karyawan...</option>
                    @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl"
                        style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                    Assign
                </button>
            </form>
        </div>
        @endif

        {{-- Quick actions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('manager.tasks.edit', $task->id) }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-gray-700 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Tugas
                </a>
                <form method="POST" action="{{ route('manager.tasks.destroy', $task->id) }}"
                      x-data
                      @submit.prevent="if(confirm('Hapus tugas ini beserta semua assignment?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm text-red-600 rounded-xl hover:bg-red-50 transition-colors border border-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Tugas
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('manager.tasks.index') }}"
           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-medium text-gray-600
                  bg-white rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>
    </div>
</div>

@endsection
