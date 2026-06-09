<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_by',
        'progress',
        'completion_notes',
        'manager_notes',
        'communication_note',
        'revision_count',
        'kpi_score',
        'assigned_at',
        'submitted_at',
        'started_at',
        'reviewed_at',
    ];

    protected $casts = [
        'assigned_at'  => 'datetime',
        'submitted_at' => 'datetime',
        'started_at'   => 'datetime',
        'reviewed_at'  => 'datetime',
        'kpi_score'    => 'decimal:1',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AssignmentActivity::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssignmentLog::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AssignmentAttachment::class);
    }

    /**
     * Apakah laporan dikirim setelah melewati tenggat (untuk KPI).
     */
    public function isLate(): bool
    {
        return $this->submitted_at
            && $this->task?->due_date
            && $this->submitted_at->gt($this->task->due_date);
    }

    /**
     * Hitung skor KPI assignment ini.
     * Mulai 10; telat -1; tiap revisi -2. Bisa negatif.
     */
    public function computeKpiScore(): float
    {
        $score = 10;

        if ($this->isLate()) {
            $score -= 1;
        }

        $score -= 2 * (int) $this->revision_count;

        return (float) $score;
    }

    /**
     * Durasi pengerjaan aktif (mulai -> submit) dalam menit, atau null.
     */
    public function workDurationMinutes(): ?int
    {
        if (! $this->started_at || ! $this->submitted_at) {
            return null;
        }

        return (int) $this->started_at->diffInMinutes($this->submitted_at);
    }

    /**
     * Durasi pengerjaan dalam format manusiawi (mis. "2 jam 15 menit").
     */
    public function workDurationHuman(): ?string
    {
        $minutes = $this->workDurationMinutes();

        if ($minutes === null) {
            return null;
        }

        if ($minutes < 60) {
            return "{$minutes} menit";
        }

        $hours = intdiv($minutes, 60);
        $rest  = $minutes % 60;

        return $rest > 0 ? "{$hours} jam {$rest} menit" : "{$hours} jam";
    }
}
