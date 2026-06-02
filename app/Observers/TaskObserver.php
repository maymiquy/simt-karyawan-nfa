<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Task;

class TaskObserver
{
    public function created(Task $task): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id() ?? $task->created_by,
            'action'      => 'task_created',
            'description' => "Task dibuat: \"{$task->title}\"",
        ]);
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('status')) {
            $old = $task->getOriginal('status');
            $new = $task->status;

            ActivityLog::create([
                'user_id'     => auth()->id() ?? $task->created_by,
                'action'      => 'task_status_changed',
                'description' => "Status task \"{$task->title}\" berubah dari {$old} menjadi {$new}",
            ]);

            return;
        }

        ActivityLog::create([
            'user_id'     => auth()->id() ?? $task->created_by,
            'action'      => 'task_updated',
            'description' => "Task diperbarui: \"{$task->title}\"",
        ]);
    }

    public function deleted(Task $task): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id() ?? $task->created_by,
            'action'      => 'task_deleted',
            'description' => "Task dihapus: \"{$task->title}\"",
        ]);
    }
}
