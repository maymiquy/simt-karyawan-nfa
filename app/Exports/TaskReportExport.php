<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaskReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private array $filters,
        private User  $manager,
    ) {}

    public function collection()
    {
        $isAdmin = $this->manager->hasRole('Admin');
        $query   = $isAdmin ? Task::query() : Task::where('created_by', $this->manager->id);

        $query->with(['creator', 'assignments.user']);

        if (! empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }
        if (! empty($this->filters['to'])) {
            $query->whereDate('created_at', '<=', $this->filters['to']);
        }
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (! empty($this->filters['employee_id'])) {
            $query->whereHas('assignments', fn ($q) => $q->where('user_id', $this->filters['employee_id']));
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return ['No', 'Judul Tugas', 'Prioritas', 'Status', 'Tenggat', 'Karyawan', 'Dibuat Oleh', 'Tanggal Dibuat'];
    }

    public function map($task): array
    {
        static $no = 0;
        $no++;

        $priorityMap = ['high' => 'Tinggi', 'medium' => 'Sedang', 'low' => 'Rendah'];
        $statusMap   = [
            'pending'     => 'Pending',
            'in_progress' => 'Sedang Proses',
            'completed'   => 'Selesai',
            'overdue'     => 'Terlambat',
            'cancelled'   => 'Dibatalkan',
        ];

        return [
            $no,
            $task->title,
            $priorityMap[$task->priority] ?? $task->priority,
            $statusMap[$task->status] ?? $task->status,
            $task->due_date?->format('d/m/Y') ?? '—',
            $task->assignments->pluck('user.name')->filter()->implode(', ') ?: '—',
            $task->creator?->name ?? '—',
            $task->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Tugas';
    }
}
