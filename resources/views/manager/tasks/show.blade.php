@extends('layouts.manager')

@section('title', $task->title)
@section('page-title', 'Detail Tugas')

@section('header-actions')
<a href="{{ route('manager.tasks.edit', $task->id) }}"
   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
    Edit
</a>
@endsection

@section('content')

<div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mb-5">
    <a href="{{ route('manager.tasks.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Kelola Tugas</a>
    <span>/</span>
    <span class="text-gray-600 dark:text-gray-300 font-medium truncate max-w-xs">{{ $task->title }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ===== KIRI — Info & Assignee Tracker ===== --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 sm:p-6 transition-colors duration-200">
            <div class="flex flex-wrap gap-2 mb-3">
                <x-status-badge :status="$task->status"/>
                <x-priority-badge :priority="$task->priority"/>
                @if ($task->is_overdue)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-800">Terlambat</span>
                @endif
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $task->title }}</h2>
            @if ($task->description)
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $task->description }}</p>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">Tidak ada deskripsi.</p>
            @endif

            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div>
                    <dt class="text-gray-400 dark:text-gray-500 text-xs mb-0.5">Dibuat oleh</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $task->creator?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-500 text-xs mb-0.5">Tanggal Dibuat</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $task->created_at->translatedFormat('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-500 text-xs mb-0.5">Tenggat</dt>
                    <dd class="font-medium {{ $task->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-gray-200' }}">
                        {{ $task->due_date?->translatedFormat('d M Y, H:i') ?? '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Assignee Tracker --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Assignee, Aktivitas & Riwayat</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $task->assignments->count() }} karyawan</span>
            </div>

            @if ($task->assignments->isEmpty())
                <x-empty-state title="Belum ada karyawan yang di-assign." />
            @else
                <div class="space-y-3">
                    @foreach ($task->assignments as $a)
                    <div class="border border-gray-100 dark:border-gray-700 rounded-xl p-4"
                         x-data="{ showReview: false, showTimeline: false }">

                        {{-- Header assignee --}}
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                 style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">
                                {{ strtoupper(substr($a->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $a->user?->name ?? '—' }}</p>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        @if($a->kpi_score !== null)
                                            <span class="text-xs font-bold px-2 py-0.5 rounded
                                                {{ $a->kpi_score >= 8 ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : ($a->kpi_score >= 5 ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300') }}">
                                                {{ rtrim(rtrim(number_format($a->kpi_score,1),'0'),'.') }}/10
                                            </span>
                                        @endif
                                        <x-status-badge :status="$a->progress" />
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    {{ $a->user?->email }}
                                    @if($a->submitted_at) · Dikirim {{ $a->submitted_at->translatedFormat('d M, H:i') }} @endif
                                    @if($a->workDurationHuman()) · {{ $a->workDurationHuman() }} @endif
                                    @if($a->revision_count > 0) · <span class="text-amber-600 dark:text-amber-400">{{ $a->revision_count }}x revisi</span> @endif
                                </p>
                            </div>
                        </div>

                        {{-- Aktivitas dilaporkan --}}
                        @if($a->activities->isNotEmpty())
                        <ul class="mt-3 space-y-1.5">
                            @foreach($a->activities as $act)
                            <li class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-300">
                                <span class="mt-1 w-1.5 h-1.5 rounded-full shrink-0 {{ $act->status === 'done' ? 'bg-green-500' : 'bg-amber-400' }}"></span>
                                <span>{{ $act->description }}@if($act->status === 'blocked')<span class="text-amber-600 dark:text-amber-400"> (terkendala)</span>@endif</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        {{-- Catatan komunikasi karyawan --}}
                        @if($a->communication_note)
                        <div class="mt-2 p-2.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 text-xs text-indigo-700 dark:text-indigo-300">
                            <span class="font-medium">Catatan karyawan:</span> {{ $a->communication_note }}
                        </div>
                        @endif

                        {{-- Lampiran bukti --}}
                        @if($a->attachments->isNotEmpty())
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach($a->attachments as $att)
                            <a href="{{ $att->url() }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[11px] bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-3.5 h-3.5 {{ $att->isImage() ? 'text-blue-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span class="truncate max-w-[120px]">{{ $att->original_name }}</span>
                            </a>
                            @endforeach
                        </div>
                        @endif

                        {{-- Catatan manager --}}
                        @if ($a->manager_notes)
                        <div class="mt-2 p-2.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-xs text-amber-700 dark:text-amber-300">
                            <span class="font-medium">Catatan manager:</span> {{ $a->manager_notes }}
                        </div>
                        @endif

                        {{-- Toggle timeline --}}
                        <button @click="showTimeline = !showTimeline" class="mt-3 text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                            <span x-text="showTimeline ? 'Sembunyikan riwayat' : 'Lihat riwayat / timeline'"></span>
                            <svg class="w-3 h-3 transition-transform" :class="showTimeline ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="showTimeline" x-transition style="display:none" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <x-assignment-timeline :logs="$a->logs" :show-actor="true" />
                        </div>

                        {{-- Review actions (hanya saat submitted) --}}
                        @if ($a->progress === 'submitted')
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <div x-show="!showReview" class="flex items-center gap-2">
                                <button @click="showReview = true"
                                        class="flex-1 px-3 py-2 text-xs font-semibold text-white rounded-lg"
                                        style="background: linear-gradient(135deg, #16a34a, #22c55e)">Setujui Laporan</button>
                                <button @click="showReview = true"
                                        class="flex-1 px-3 py-2 text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-800/40 transition-colors">Minta Revisi</button>
                            </div>
                            <div x-show="showReview" x-transition style="display:none">
                                <form method="POST" action="{{ route('manager.assignments.review', $a->id) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" x-ref="reviewAction" value="approve">
                                    <textarea name="manager_notes" rows="3" placeholder="Catatan / alasan revisi (opsional)..."
                                              class="block w-full px-3 py-2 mb-2 text-xs text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none placeholder-gray-400 dark:placeholder-gray-500"></textarea>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" @click="$refs.reviewAction.value = 'approve'"
                                                class="flex-1 px-3 py-2 text-xs font-semibold text-white rounded-lg" style="background: linear-gradient(135deg, #16a34a, #22c55e)">Setujui</button>
                                        <button type="submit" @click="$refs.reviewAction.value = 'revision'"
                                                class="flex-1 px-3 py-2 text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-800/40 transition-colors">Minta Revisi</button>
                                        <button type="button" @click="showReview = false"
                                                class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        {{-- Remove assignment --}}
                        <form method="POST" action="{{ route('manager.assignments.destroy', $a->id) }}" class="mt-2" x-data
                              @submit.prevent="if(confirm('Hapus assignment ini?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 dark:text-red-500 hover:text-red-600 dark:hover:text-red-400 hover:underline transition-colors">Hapus assignment</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== KANAN — Aksi ===== --}}
    <div class="space-y-4">

        @if ($employees->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">+ Assign Karyawan</h3>
            <form method="POST" action="{{ route('manager.assignments.store', $task->id) }}">
                @csrf
                <select name="user_id" required
                        class="block w-full px-3 py-2.5 text-sm text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3">
                    <option value="">Pilih karyawan...</option>
                    @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl" style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">Assign</button>
            </form>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('manager.tasks.edit', $task->id) }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border border-gray-100 dark:border-gray-700">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Tugas
                </a>
                <form method="POST" action="{{ route('manager.tasks.destroy', $task->id) }}" x-data
                      @submit.prevent="if(confirm('Hapus tugas ini beserta semua assignment?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm text-red-600 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border border-gray-100 dark:border-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Tugas
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('manager.tasks.index') }}"
           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>
</div>

@endsection
