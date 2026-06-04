<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueTasksTable extends BaseWidget
{
    protected static ?string $heading = 'Tugas Terlambat';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Task::overdue()->with(['creator', 'assignments.user'])->latest('due_date'))
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->weight('medium')
                    ->limit(40)
                    ->url(fn (Task $record) => TaskResource::getUrl('view', ['record' => $record])),

                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        'low'    => 'success',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high'   => 'Tinggi',
                        'medium' => 'Sedang',
                        'low'    => 'Rendah',
                        default  => $state,
                    }),

                TextColumn::make('due_date')
                    ->label('Tenggat')
                    ->date('d M Y')
                    ->color('danger')
                    ->description(fn (Task $record) => $record->due_date?->diffForHumans()),

                TextColumn::make('assignments_count')
                    ->label('Assignee')
                    ->counts('assignments')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('creator.name')
                    ->label('Dibuat oleh'),
            ])
            ->emptyStateHeading('Tidak ada tugas terlambat')
            ->emptyStateDescription('Semua tugas sedang berjalan tepat waktu.');
    }
}
