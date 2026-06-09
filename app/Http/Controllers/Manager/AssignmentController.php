<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentLog;
use App\Models\Task;
use App\Models\User;
use App\Notifications\EmployeeAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function store(Request $request, int $taskId): RedirectResponse
    {
        $isAdmin = Auth::user()->hasRole('Admin');
        $task = $isAdmin
            ? Task::findOrFail($taskId)
            : Task::where('created_by', Auth::id())->findOrFail($taskId);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $alreadyAssigned = $task->assignments()->where('user_id', $request->user_id)->exists();
        if ($alreadyAssigned) {
            return back()->with('error', 'Karyawan sudah di-assign ke tugas ini.');
        }

        Assignment::create([
            'task_id'     => $task->id,
            'user_id'     => $request->user_id,
            'assigned_by' => Auth::id(),
            'progress'    => 'not_started',
        ]);

        return back()->with('success', 'Karyawan berhasil di-assign.');
    }

    public function review(Request $request, int $id): RedirectResponse
    {
        $assignment = Assignment::whereHas('task', function ($q) {
            $isAdmin = Auth::user()->hasRole('Admin');
            if (! $isAdmin) {
                $q->where('created_by', Auth::id());
            }
        })->findOrFail($id);

        $request->validate([
            'action'        => ['required', 'in:approve,revision'],
            'manager_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Hanya assignment yang sudah disubmit yang bisa direview.
        if ($assignment->progress !== 'submitted') {
            return back()->with('error', 'Hanya laporan yang sudah dikirim yang bisa direview.');
        }

        DB::transaction(function () use ($assignment, $request) {
            if ($request->action === 'approve') {
                // Finalisasi KPI saat disetujui.
                $kpi = $assignment->computeKpiScore();

                $assignment->update([
                    'progress'      => 'done',
                    'manager_notes' => $request->manager_notes,
                    'reviewed_at'   => now(),
                    'kpi_score'     => $kpi,
                ]);

                AssignmentLog::create([
                    'assignment_id' => $assignment->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'approved',
                    'notes'         => $request->manager_notes,
                    'meta'          => [
                        'kpi'  => $kpi,
                        'late' => $assignment->isLate(),
                    ],
                ]);
            } else {
                $assignment->update([
                    'progress'       => 'revision',
                    'manager_notes'  => $request->manager_notes,
                    'reviewed_at'    => now(),
                    'revision_count' => $assignment->revision_count + 1,
                ]);

                AssignmentLog::create([
                    'assignment_id' => $assignment->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'revised',
                    'notes'         => $request->manager_notes,
                    'meta'          => ['revision_no' => $assignment->revision_count],
                ]);

                $assignment->user?->notify(new EmployeeAlert(
                    type: 'revision',
                    title: 'Tugas diminta revisi',
                    message: "Tugas \"{$assignment->task?->title}\" perlu direvisi.",
                    assignmentId: $assignment->id,
                ));
            }
        });

        $message = $request->action === 'approve'
            ? 'Laporan disetujui & KPI dihitung.'
            : 'Tugas dikembalikan untuk revisi.';

        return back()->with('success', $message);
    }

    public function destroy(int $id): RedirectResponse
    {
        $assignment = Assignment::whereHas('task', function ($q) {
            $isAdmin = Auth::user()->hasRole('Admin');
            if (! $isAdmin) {
                $q->where('created_by', Auth::id());
            }
        })->findOrFail($id);

        $assignment->delete();

        return back()->with('success', 'Assignment berhasil dihapus.');
    }
}
