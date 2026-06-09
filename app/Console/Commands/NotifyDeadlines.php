<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Notifications\EmployeeAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyDeadlines extends Command
{
    protected $signature   = 'tasks:notify-deadlines';
    protected $description = 'Kirim notifikasi in-app untuk tugas yang tenggatnya < 24 jam dan belum selesai';

    public function handle(): int
    {
        $assignments = Assignment::with(['task', 'user'])
            ->whereIn('progress', ['not_started', 'on_progress', 'submitted', 'revision'])
            ->whereHas('task', fn ($q) => $q->whereBetween('due_date', [now(), now()->addDay()]))
            ->get();

        $sent = 0;

        foreach ($assignments as $a) {
            if (! $a->user) {
                continue;
            }

            // Hindari duplikat: sudah ada notifikasi deadline untuk assignment ini dalam 24 jam terakhir?
            $exists = DB::table('notifications')
                ->where('type', EmployeeAlert::class)
                ->where('notifiable_id', $a->user_id)
                ->where('created_at', '>=', now()->subDay())
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.type')) = 'deadline'")
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assignment_id')) = ?", [(string) $a->id])
                ->exists();

            if ($exists) {
                continue;
            }

            $a->user->notify(new EmployeeAlert(
                type: 'deadline',
                title: 'Tenggat mendekat',
                message: "Tugas \"{$a->task?->title}\" jatuh tempo " . $a->task?->due_date?->translatedFormat('d M, H:i') . '.',
                assignmentId: $a->id,
            ));

            $sent++;
        }

        $this->info("Notifikasi tenggat terkirim: {$sent}.");

        return self::SUCCESS;
    }
}
