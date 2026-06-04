@extends('layouts.employee')

@section('title', $assignment->task?->title ?? 'Detail Tugas')
@section('page-title', 'Detail Tugas')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-xs text-gray-400 mb-5">
    <a href="{{ route('employee.dashboard') }}" class="hover:text-gray-600 transition-colors">Dashboard</a>
    <span>/</span>
    <a href="{{ route('employee.tasks.index') }}" class="hover:text-gray-600 transition-colors">Tugas Saya</a>
    <span>/</span>
    <span class="text-gray-600 font-medium truncate max-w-xs">{{ $assignment->task?->title }}</span>
</div>

@php
    $task = $assignment->task;
    $progress = $assignment->progress;
    $isOverdue = $task?->due_date && $task->due_date->isPast() && ! in_array($progress, ['done']);

    $progressMap = [
        'not_started' => ['label' => 'Belum Mulai',   'class' => 'bg-gray-100 text-gray-600'],
        'on_progress' => ['label' => 'Sedang Proses',  'class' => 'bg-blue-50 text-blue-700'],
        'done'        => ['label' => 'Selesai',        'class' => 'bg-green-50 text-green-700'],
        'revision'    => ['label' => 'Perlu Revisi',   'class' => 'bg-amber-50 text-amber-700'],
    ];
    $pb = $progressMap[$progress] ?? ['label' => $progress, 'class' => 'bg-gray-100 text-gray-600'];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ===== PANEL KIRI — Info Tugas ===== --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Judul & badges --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $pb['class'] }}">
                    {{ $pb['label'] }}
                </span>

                @if ($task?->priority === 'high')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Prioritas Tinggi
                    </span>
                @elseif ($task?->priority === 'medium')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Prioritas Sedang
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Prioritas Rendah
                    </span>
                @endif

                @if ($isOverdue)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                        Terlambat
                    </span>
                @endif
            </div>

            <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $task?->title }}</h2>

            @if ($task?->description)
                <p class="text-sm text-gray-600 leading-relaxed">{{ $task->description }}</p>
            @else
                <p class="text-sm text-gray-400 italic">Tidak ada deskripsi.</p>
            @endif
        </div>

        {{-- Meta info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Tugas</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs mb-0.5">Dibuat oleh</dt>
                    <dd class="font-medium text-gray-800">{{ $task?->creator?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-0.5">Tanggal Dibuat</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $task?->created_at?->translatedFormat('d M Y') ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-0.5">Tanggal Tenggat</dt>
                    <dd class="font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">
                        @if ($task?->due_date)
                            {{ $task->due_date->translatedFormat('d M Y') }}
                            @if ($isOverdue)
                                <span class="text-xs ml-1">(terlambat {{ $task->due_date->diffForHumans() }})</span>
                            @elseif ($task->due_date->isToday())
                                <span class="text-xs text-amber-600 ml-1">(hari ini)</span>
                            @elseif ($task->due_date->isTomorrow())
                                <span class="text-xs text-amber-600 ml-1">(besok)</span>
                            @endif
                        @else
                            <span class="text-gray-400">Tidak ada tenggat</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-0.5">Laporan Dikirim</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $assignment->submitted_at?->translatedFormat('d M Y, H:i') ?? '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Catatan Manager (jika ada) --}}
        @if ($assignment->manager_notes)
        <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-800 mb-1">Catatan dari Manager</p>
                    <p class="text-sm text-amber-700 leading-relaxed">{{ $assignment->manager_notes }}</p>
                    @if ($assignment->reviewed_at)
                        <p class="text-xs text-amber-500 mt-1.5">
                            Ditinjau: {{ $assignment->reviewed_at->translatedFormat('d M Y, H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Laporan penyelesaian yang sudah dikirim --}}
        @if ($assignment->completion_notes && $progress === 'done')
        <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-green-800 mb-1">Laporan Penyelesaian Terkirim</p>
                    <p class="text-sm text-green-700 leading-relaxed">{{ $assignment->completion_notes }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ===== PANEL KANAN — Aksi ===== --}}
    <div class="space-y-4">

        {{-- Status card + aksi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5" x-data="{ showForm: false }">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Update Status</h3>

            @if ($progress === 'not_started')
                {{-- Mulai kerjakan --}}
                <p class="text-xs text-gray-500 mb-4">Klik tombol di bawah untuk mengubah status menjadi "Sedang Proses".</p>
                <form method="POST" action="{{ route('employee.tasks.progress', $assignment->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="progress" value="on_progress">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white rounded-xl transition-all
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                            style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mulai Kerjakan
                    </button>
                </form>

            @elseif ($progress === 'on_progress')
                {{-- Kirim laporan --}}
                <div x-show="!showForm">
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-blue-50 border border-blue-100 mb-4">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-xs font-medium text-blue-700">Sedang dikerjakan</span>
                    </div>
                    <button @click="showForm = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white rounded-xl transition-all
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1"
                            style="background: linear-gradient(135deg, #16a34a, #22c55e)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Kirim Laporan Selesai
                    </button>
                </div>

                {{-- Form Laporan --}}
                <div x-show="showForm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display:none">
                    <form method="POST" action="{{ route('employee.tasks.submit', $assignment->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Deskripsi Pekerjaan yang Sudah Dilakukan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="completion_notes" rows="5" required minlength="20"
                                      placeholder="Jelaskan apa saja yang sudah Anda kerjakan dan hasilnya... (minimal 20 karakter)"
                                      class="block w-full px-3 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-xl
                                             focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                                             hover:border-gray-300 transition-colors resize-none">{{ old('completion_notes') }}</textarea>
                            @error('completion_notes')
                                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all"
                                    style="background: linear-gradient(135deg, #16a34a, #22c55e)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Kirim
                            </button>
                            <button type="button" @click="showForm = false"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>

            @elseif ($progress === 'revision')
                {{-- Revisi --}}
                <div class="flex items-center gap-2 p-3 rounded-xl bg-amber-50 border border-amber-100 mb-4">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="text-xs font-medium text-amber-700">Perlu direvisi oleh Manager</span>
                </div>
                <form method="POST" action="{{ route('employee.tasks.progress', $assignment->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="progress" value="on_progress">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white rounded-xl transition-all"
                            style="background: linear-gradient(135deg, #d97706, #f59e0b)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Kerjakan Ulang
                    </button>
                </form>

            @elseif ($progress === 'done')
                {{-- Selesai --}}
                <div class="flex flex-col items-center py-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">Tugas Selesai</p>
                    @if ($assignment->submitted_at)
                        <p class="text-xs text-gray-400 mt-1">
                            Dikirim {{ $assignment->submitted_at->translatedFormat('d M Y') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Assignee list (semua yang mengerjakan task ini) --}}
        @if ($task?->assignments && $task->assignments->count() > 1)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Tim yang Mengerjakan</h3>
            <div class="space-y-2">
                @foreach ($task->assignments as $a)
                @php
                    $aBadge = $progressMap[$a->progress] ?? ['label' => $a->progress, 'class' => 'bg-gray-100 text-gray-600'];
                @endphp
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0
                                {{ $a->user_id === Auth::id() ? '' : 'bg-gray-300' }}"
                         style="{{ $a->user_id === Auth::id() ? 'background: linear-gradient(135deg, #1d4ed8, #2563eb)' : '' }}">
                        {{ strtoupper(substr($a->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 truncate">
                            {{ $a->user?->name ?? '—' }}
                            @if ($a->user_id === Auth::id()) <span class="text-gray-400">(Anda)</span> @endif
                        </p>
                    </div>
                    <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-md {{ $aBadge['class'] }}">
                        {{ $aBadge['label'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Back button --}}
        <a href="{{ route('employee.tasks.index') }}"
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
