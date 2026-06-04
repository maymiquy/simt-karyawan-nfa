<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Console\Command;

class UpdateOverdueTasks extends Command
{
    protected $signature   = 'tasks:check-overdue';
    protected $description = 'Tandai tugas sebagai overdue jika due_date sudah lewat dan belum selesai';

    public function handle(): int
    {
        $tasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('Tidak ada tugas yang perlu ditandai overdue.');
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($tasks as $task) {
            Task::withoutEvents(function () use ($task) {
                $task->update(['status' => 'overdue']);
            });

            ActivityLog::create([
                'user_id'     => null,
                'action'      => 'task_status_changed',
                'description' => "Tugas #{$task->id} \"{$task->title}\" otomatis ditandai overdue oleh scheduler.",
            ]);

            $count++;
        }

        $this->info("Berhasil menandai {$count} tugas sebagai overdue.");

        return self::SUCCESS;
    }
}
