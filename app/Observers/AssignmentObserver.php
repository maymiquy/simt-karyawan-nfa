<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentLog;
use App\Models\Task;
use App\Notifications\EmployeeAlert;

class AssignmentObserver
{
    public function creating(Assignment $assignment): void
    {
        // Catat waktu assign otomatis (referensi untuk timeline & KPI).
        if (! $assignment->assigned_at) {
            $assignment->assigned_at = now();
        }
    }

    public function created(Assignment $assignment): void
    {
        $employeeName = $assignment->user->name ?? 'Unknown';
        $taskTitle    = $assignment->task->title ?? 'Unknown';

        // Audit log global (panel admin).
        ActivityLog::create([
            'user_id'     => auth()->id() ?? $assignment->assigned_by,
            'action'      => 'assignment_created',
            'description' => "{$employeeName} ditugaskan ke task \"{$taskTitle}\"",
        ]);

        // Timeline lifecycle assignment (dashboard manager & karyawan).
        AssignmentLog::create([
            'assignment_id' => $assignment->id,
            'user_id'       => auth()->id() ?? $assignment->assigned_by,
            'type'          => 'created',
            'notes'         => null,
            'meta'          => null,
        ]);

        // Notifikasi in-app ke karyawan yang ditugaskan.
        if ($assignment->user) {
            $assignment->user->notify(new EmployeeAlert(
                type: 'assigned',
                title: 'Tugas baru ditugaskan',
                message: "Anda mendapat tugas: \"{$taskTitle}\".",
                assignmentId: $assignment->id,
            ));
        }
    }

    public function updated(Assignment $assignment): void
    {
        if (! $assignment->wasChanged('progress')) {
            return;
        }

        $employeeName = $assignment->user->name ?? 'Unknown';
        $taskTitle    = $assignment->task->title ?? 'Unknown';
        $newProgress  = $assignment->progress;

        ActivityLog::create([
            'user_id'     => auth()->id() ?? $assignment->user_id,
            'action'      => 'assignment_progress_updated',
            'description' => "Progress {$employeeName} pada task \"{$taskTitle}\" menjadi {$newProgress}",
        ]);

        $this->syncTaskStatus($assignment);
    }

    public function deleted(Assignment $assignment): void
    {
        $employeeName = $assignment->user->name ?? 'Unknown';
        $taskTitle    = $assignment->task->title ?? 'Unknown';

        ActivityLog::create([
            'user_id'     => auth()->id() ?? $assignment->assigned_by,
            'action'      => 'assignment_deleted',
            'description' => "Assignment {$employeeName} dari task \"{$taskTitle}\" dihapus",
        ]);
    }

    /**
     * Sync the parent task status based on all assignment progresses.
     * Runs without triggering observer loop via withoutEvents.
     */
    private function syncTaskStatus(Assignment $assignment): void
    {
        $task = $assignment->task;

        if (! $task || in_array($task->status, ['cancelled'])) {
            return;
        }

        $allProgresses = $task->assignments()->pluck('progress')->toArray();

        if (empty($allProgresses)) {
            return;
        }

        $allDone     = collect($allProgresses)->every(fn ($p) => $p === 'done');
        $anyProgress = collect($allProgresses)->contains(fn ($p) => in_array($p, ['on_progress', 'submitted', 'done', 'revision']));

        $newStatus = match (true) {
            $allDone     => 'completed',
            $anyProgress => 'in_progress',
            default      => $task->status,
        };

        if ($newStatus !== $task->status) {
            Task::withoutEvents(function () use ($task, $newStatus) {
                $task->update(['status' => $newStatus]);
            });

            ActivityLog::create([
                'user_id'     => auth()->id() ?? $assignment->assigned_by,
                'action'      => 'task_status_changed',
                'description' => "Status task \"{$task->title}\" otomatis berubah menjadi {$newStatus}",
            ]);
        }
    }
}
