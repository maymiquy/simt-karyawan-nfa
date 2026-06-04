<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'action'       => ['required', 'in:approve,revision'],
            'manager_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $progress = $request->action === 'approve' ? 'done' : 'revision';

        $assignment->update([
            'progress'      => $progress,
            'manager_notes' => $request->manager_notes,
            'reviewed_at'   => now(),
        ]);

        $message = $request->action === 'approve'
            ? 'Laporan disetujui.'
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
