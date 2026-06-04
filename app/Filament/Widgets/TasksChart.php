<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TasksChart extends ChartWidget
{
    protected ?string $heading = 'Tugas Selesai vs Terlambat (7 Hari)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels    = [];
        $completed = [];
        $overdue   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->translatedFormat('d M');

            $completed[] = Task::where('status', 'completed')
                ->whereDate('updated_at', $date->toDateString())
                ->count();

            $overdue[] = Task::overdue()
                ->whereDate('due_date', $date->toDateString())
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Selesai',
                    'data'            => $completed,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor'     => 'rgba(34, 197, 94, 1)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Terlambat',
                    'data'            => $overdue,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor'     => 'rgba(239, 68, 68, 1)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
