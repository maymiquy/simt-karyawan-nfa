<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentLog extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'type',  // created | started | submitted | revised | approved
        'notes',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Label & warna untuk UI timeline. */
    public function presentation(): array
    {
        return match ($this->type) {
            'created'   => ['label' => 'Tugas dibuat & ditugaskan', 'color' => 'gray',  'icon' => 'plus'],
            'started'   => ['label' => 'Mulai dikerjakan',          'color' => 'blue',  'icon' => 'play'],
            'submitted' => ['label' => 'Laporan dikirim',           'color' => 'indigo','icon' => 'paper'],
            'revised'   => ['label' => 'Diminta revisi',            'color' => 'amber', 'icon' => 'revise'],
            'approved'  => ['label' => 'Disetujui',                 'color' => 'green', 'icon' => 'check'],
            default     => ['label' => $this->type,                 'color' => 'gray',  'icon' => 'dot'],
        };
    }
}
