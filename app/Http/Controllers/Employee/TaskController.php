<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Assignment::query()
            ->with(['task', 'task.creator'])
            ->leftJoin('tasks', 'tasks.id', '=', 'assignments.task_id')
            ->where('assignments.user_id', Auth::id())
            ->select('assignments.*');

        if ($request->filled('status')) {
            $query->where('assignments.progress', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('tasks.title', 'like', '%' . $request->search . '%');
        }

        // Tab cepat berdasarkan tenggat
        switch ($request->get('due')) {
            case 'today':
                $query->whereDate('tasks.due_date', today());
                break;
            case 'week':
                $query->whereBetween('tasks.due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'overdue':
                $query->where('tasks.due_date', '<', now())
                    ->where('assignments.progress', '!=', 'done');
                break;
        }

        // Pengurutan
        switch ($request->get('sort', 'due')) {
            case 'priority':
                $query->orderByRaw("FIELD(tasks.priority,'high','medium','low')");
                break;
            case 'duration':
                // Durasi pengerjaan terlama dulu; yang belum ada durasi di akhir.
                $query->orderByRaw('TIMESTAMPDIFF(MINUTE, assignments.started_at, assignments.submitted_at) DESC');
                break;
            default: // 'due' — tenggat terdekat
                $query->orderByRaw('tasks.due_date IS NULL, tasks.due_date ASC');
                break;
        }

        $assignments = $query->paginate(10)->withQueryString();

        return view('employee.tasks.index', compact('assignments'));
    }

    public function show(int $id): View
    {
        $assignment = Assignment::with([
            'task', 'task.creator', 'task.assignments.user',
            'activities',
            'attachments',
            'logs' => fn ($q) => $q->with('user')->oldest(),
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('employee.tasks.show', compact('assignment'));
    }

    public function updateProgress(Request $request, int $id): RedirectResponse
    {
        $assignment = Assignment::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'progress' => ['required', 'in:on_progress'],
        ]);

        if (! in_array($assignment->progress, ['not_started', 'revision'])) {
            return back()->with('error', 'Status tugas tidak dapat diubah.');
        }

        $isRework = $assignment->progress === 'revision';

        $data = ['progress' => 'on_progress'];
        if (! $assignment->started_at) {
            $data['started_at'] = now();
        }
        $assignment->update($data);

        if (! $isRework) {
            AssignmentLog::create([
                'assignment_id' => $assignment->id,
                'user_id'       => Auth::id(),
                'type'          => 'started',
            ]);
        }

        return back()->with('success', $isRework ? 'Tugas dikerjakan ulang.' : 'Tugas mulai dikerjakan.');
    }

    public function submitReport(Request $request, int $id): RedirectResponse
    {
        $assignment = Assignment::where('user_id', Auth::id())->findOrFail($id);

        if (! in_array($assignment->progress, ['on_progress', 'revision'])) {
            return back()->with('error', 'Tugas tidak dalam status yang bisa dikirim.');
        }

        $validated = $request->validate([
            'activities'               => ['required', 'array', 'min:1'],
            'activities.*.description' => ['required', 'string', 'min:3', 'max:500'],
            'activities.*.status'      => ['required', 'in:done,blocked'],
            'communication_note'       => ['nullable', 'string', 'max:1000'],
            'attachments'              => ['nullable', 'array', 'max:5'],
            'attachments.*'            => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'activities.required'               => 'Tambahkan minimal satu aktivitas.',
            'activities.min'                    => 'Tambahkan minimal satu aktivitas.',
            'activities.*.description.required' => 'Deskripsi aktivitas wajib diisi.',
            'activities.*.description.min'      => 'Deskripsi aktivitas minimal 3 karakter.',
            'attachments.*.mimes'               => 'Lampiran harus berupa JPG, PNG, atau PDF.',
            'attachments.*.max'                 => 'Ukuran lampiran maksimal 5 MB.',
        ]);

        $activities   = $validated['activities'];
        $blockedCount = collect($activities)->where('status', 'blocked')->count();

        DB::transaction(function () use ($assignment, $activities, $validated, $blockedCount, $request) {
            $assignment->activities()->delete();
            foreach ($activities as $item) {
                $assignment->activities()->create([
                    'description' => $item['description'],
                    'status'      => $item['status'],
                ]);
            }

            // Simpan lampiran (jika ada) — tetap dipertahankan antar submit.
            foreach ($request->file('attachments', []) as $file) {
                $path = $file->store("attachments/{$assignment->id}", 'public');
                $assignment->attachments()->create([
                    'uploaded_by'   => Auth::id(),
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }

            $assignment->update([
                'progress'           => 'submitted',
                'submitted_at'       => now(),
                'communication_note' => $validated['communication_note'] ?? null,
            ]);

            AssignmentLog::create([
                'assignment_id' => $assignment->id,
                'user_id'       => Auth::id(),
                'type'          => 'submitted',
                'notes'         => $validated['communication_note'] ?? null,
                'meta'          => [
                    'activity_count'   => count($activities),
                    'blocked_count'    => $blockedCount,
                    'attachment_count' => count($request->file('attachments', [])),
                ],
            ]);
        });

        return back()->with('success', 'Laporan aktivitas berhasil dikirim. Menunggu review manager.');
    }
}
