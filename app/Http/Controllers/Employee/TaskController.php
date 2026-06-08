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
        $query = Assignment::with(['task', 'task.creator'])
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('progress', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('task', fn ($q) => $q->where('title', 'like', "%{$search}%"));
        }

        $assignments = $query->latest()->paginate(10)->withQueryString();

        return view('employee.tasks.index', compact('assignments'));
    }

    public function show(int $id): View
    {
        $assignment = Assignment::with([
            'task', 'task.creator', 'task.assignments.user',
            'activities',
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

        // Hanya boleh mulai dari belum mulai / revisi.
        if (! in_array($assignment->progress, ['not_started', 'revision'])) {
            return back()->with('error', 'Status tugas tidak dapat diubah.');
        }

        $isRework = $assignment->progress === 'revision';

        $data = ['progress' => 'on_progress'];
        // Set started_at hanya pertama kali mulai (jaga basis durasi pengerjaan).
        if (! $assignment->started_at) {
            $data['started_at'] = now();
        }
        $assignment->update($data);

        // Log "started" hanya saat pertama kali mulai (bukan saat rework revisi).
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
            'activities'              => ['required', 'array', 'min:1'],
            'activities.*.description'=> ['required', 'string', 'min:3', 'max:500'],
            'activities.*.status'     => ['required', 'in:done,blocked'],
            'communication_note'      => ['nullable', 'string', 'max:1000'],
        ], [
            'activities.required'              => 'Tambahkan minimal satu aktivitas.',
            'activities.min'                   => 'Tambahkan minimal satu aktivitas.',
            'activities.*.description.required'=> 'Deskripsi aktivitas wajib diisi.',
            'activities.*.description.min'     => 'Deskripsi aktivitas minimal 3 karakter.',
        ]);

        $activities   = $validated['activities'];
        $blockedCount = collect($activities)->where('status', 'blocked')->count();

        DB::transaction(function () use ($assignment, $activities, $validated, $blockedCount) {
            // Ganti daftar aktivitas dengan submission terbaru (state sprint terkini).
            $assignment->activities()->delete();
            foreach ($activities as $item) {
                $assignment->activities()->create([
                    'description' => $item['description'],
                    'status'      => $item['status'],
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
                    'activity_count' => count($activities),
                    'blocked_count'  => $blockedCount,
                ],
            ]);
        });

        return back()->with('success', 'Laporan aktivitas berhasil dikirim. Menunggu review manager.');
    }
}
