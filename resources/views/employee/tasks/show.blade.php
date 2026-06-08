@extends('layouts.employee')

@section('title', $assignment->task?->title ?? 'Detail Tugas')
@section('page-title', 'Detail Tugas')

@section('content')

<div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mb-5">
    <a href="{{ route('employee.dashboard') }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Dashboard</a>
    <span>/</span>
    <a href="{{ route('employee.tasks.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Tugas Saya</a>
    <span>/</span>
    <span class="text-gray-600 dark:text-gray-300 font-medium truncate max-w-xs">{{ $assignment->task?->title }}</span>
</div>

@php
    $task      = $assignment->task;
    $progress  = $assignment->progress;
    $isOverdue = $task?->due_date && $task->due_date->isPast() && $progress !== 'done';
    $canEdit   = in_array($progress, ['on_progress', 'revision']);
    $existing  = $assignment->activities->map(fn ($a) => ['description' => $a->description, 'status' => $a->status])->values();
    if ($existing->isEmpty()) { $existing = collect([['description' => '', 'status' => 'done']]); }
@endphp

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- ===== KIRI — Info, aktivitas, timeline ===== --}}
    <div class="lg:col-span-3 space-y-4">

        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 sm:p-6 transition-colors duration-200">
            <div class="flex flex-wrap gap-2 mb-3">
                <x-status-badge :status="$progress"/>
                <x-priority-badge :priority="$task?->priority ?? 'medium'"/>
                @if ($isOverdue)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-800">Terlambat</span>
                @endif
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $task?->title }}</h2>
            @if ($task?->description)
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $task->description }}</p>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">Tidak ada deskripsi.</p>
            @endif

            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div>
                    <dt class="text-gray-400 dark:text-gray-500 text-xs mb-0.5">Dibuat oleh</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $task?->creator?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-500 text-xs mb-0.5">Tenggat</dt>
                    <dd class="font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-gray-200' }}">
                        {{ $task?->due_date?->translatedFormat('d M Y, H:i') ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-500 text-xs mb-0.5">Mulai dikerjakan</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $assignment->started_at?->translatedFormat('d M Y, H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-500 text-xs mb-0.5">Durasi pengerjaan</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $assignment->workDurationHuman() ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Catatan manager (revisi) --}}
        @if ($assignment->manager_notes && in_array($progress, ['revision', 'done']))
        <div class="rounded-2xl border p-5 {{ $progress === 'revision' ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' }}">
            <p class="text-sm font-semibold {{ $progress === 'revision' ? 'text-amber-800 dark:text-amber-300' : 'text-green-800 dark:text-green-300' }} mb-1">
                {{ $progress === 'revision' ? 'Catatan Revisi dari Manager' : 'Catatan Manager' }}
            </p>
            <p class="text-sm {{ $progress === 'revision' ? 'text-amber-700 dark:text-amber-400' : 'text-green-700 dark:text-green-400' }} leading-relaxed">{{ $assignment->manager_notes }}</p>
        </div>
        @endif

        {{-- Daftar aktivitas yang sudah dilaporkan (read-only saat submitted/done) --}}
        @if (in_array($progress, ['submitted', 'done']) && $assignment->activities->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Aktivitas Dilaporkan</h3>
            <ul class="space-y-2">
                @foreach ($assignment->activities as $act)
                <li class="flex items-start gap-2.5">
                    @if($act->status === 'done')
                        <svg class="w-4 h-4 text-green-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    @else
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    @endif
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $act->description }}
                        @if($act->status === 'blocked')<span class="text-xs text-amber-600 dark:text-amber-400">(terkendala)</span>@endif
                    </span>
                </li>
                @endforeach
            </ul>
            @if ($assignment->communication_note)
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Catatan Komunikasi</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $assignment->communication_note }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Timeline --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Riwayat / Timeline</h3>
            <x-assignment-timeline :logs="$assignment->logs" :show-actor="true"/>
        </div>
    </div>

    {{-- ===== KANAN — Aksi ===== --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Aksi</h3>

            @if ($progress === 'not_started')
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Klik untuk mulai mengerjakan tugas. Waktu mulai akan dicatat.</p>
                <form method="POST" action="{{ route('employee.tasks.progress', $assignment->id) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="progress" value="on_progress">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white rounded-xl"
                            style="background:linear-gradient(135deg,#1d4ed8,#2563eb)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mulai Kerjakan
                    </button>
                </form>

            @elseif ($canEdit)
                {{-- Form laporan aktivitas dinamis (Alpine repeater) --}}
                @if ($progress === 'revision')
                <div class="flex items-center gap-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800 mb-4">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-xs font-medium text-amber-700 dark:text-amber-300">Perlu revisi — perbaiki & kirim ulang</span>
                </div>
                @endif

                <form method="POST" action="{{ route('employee.tasks.submit', $assignment->id) }}"
                      x-data="{ items: {{ Illuminate\Support\Js::from($existing) }} }">
                    @csrf

                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Daftar Aktivitas <span class="text-red-500">*</span>
                    </label>

                    <div class="space-y-2 mb-2">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="flex items-start gap-2">
                                <div class="flex-1">
                                    <input type="text"
                                           :name="'activities['+idx+'][description]'"
                                           x-model="item.description"
                                           placeholder="Aktivitas yang dikerjakan..."
                                           class="block w-full px-3 py-2 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <select :name="'activities['+idx+'][status]'" x-model="item.status"
                                        class="w-24 shrink-0 px-1.5 py-2 text-xs text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="done">Selesai</option>
                                    <option value="blocked">Terkendala</option>
                                </select>
                                <button type="button" @click="items.splice(idx, 1)" x-show="items.length > 1"
                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" tabindex="-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="items.push({description:'', status:'done'})"
                            class="w-full flex items-center justify-center gap-1.5 px-3 py-2 mb-4 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 border border-dashed border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Aktivitas
                    </button>

                    @error('activities') <p class="text-xs text-red-600 mb-3 -mt-2">{{ $message }}</p> @enderror
                    @error('activities.*.description') <p class="text-xs text-red-600 mb-3 -mt-2">{{ $message }}</p> @enderror

                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Catatan Komunikasi
                        <span class="font-normal text-gray-400 dark:text-gray-500">(opsional — hal yang belum dipahami / kendala)</span>
                    </label>
                    <textarea name="communication_note" rows="3" placeholder="Mis. bagian deploy belum dipahami, mohon arahan..."
                              class="block w-full px-3 py-2 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none mb-4">{{ old('communication_note', $assignment->communication_note) }}</textarea>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white rounded-xl"
                            style="background:linear-gradient(135deg,#16a34a,#22c55e)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        {{ $progress === 'revision' ? 'Kirim Ulang Laporan' : 'Kirim Laporan' }}
                    </button>
                </form>

            @elseif ($progress === 'submitted')
                <div class="flex flex-col items-center py-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Menunggu Review</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Laporan dikirim {{ $assignment->submitted_at?->translatedFormat('d M Y, H:i') }}</p>
                </div>

            @elseif ($progress === 'done')
                <div class="flex flex-col items-center py-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tugas Disetujui</p>
                    @if ($assignment->kpi_score !== null)
                        <p class="mt-2"><span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-bold
                            {{ $assignment->kpi_score >= 8 ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : ($assignment->kpi_score >= 5 ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300') }}">
                            Skor: {{ rtrim(rtrim(number_format($assignment->kpi_score,1),'0'),'.') }}/10</span></p>
                    @endif
                </div>
            @endif
        </div>

        <a href="{{ route('employee.tasks.index') }}"
           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>
</div>

@endsection
