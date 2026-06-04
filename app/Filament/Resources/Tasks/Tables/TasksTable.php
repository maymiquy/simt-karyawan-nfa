<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->weight('medium')
                    ->limit(40),

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

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'     => 'gray',
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        'overdue'     => 'danger',
                        'cancelled'   => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'     => 'Pending',
                        'in_progress' => 'Proses',
                        'completed'   => 'Selesai',
                        'overdue'     => 'Terlambat',
                        'cancelled'   => 'Dibatalkan',
                        default       => $state,
                    }),

                TextColumn::make('due_date')
                    ->label('Tenggat')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record): string =>
                        $record && $record->is_overdue ? 'danger' : 'gray'
                    )
                    ->weight(fn ($record): string =>
                        $record && $record->is_overdue ? 'bold' : 'normal'
                    ),

                TextColumn::make('creator.name')
                    ->label('Dibuat oleh')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('assignments_count')
                    ->label('Assignee')
                    ->counts('assignments')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'Sedang Proses',
                        'completed'   => 'Selesai',
                        'overdue'     => 'Terlambat',
                        'cancelled'   => 'Dibatalkan',
                    ]),

                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'high'   => 'Tinggi',
                        'medium' => 'Sedang',
                        'low'    => 'Rendah',
                    ]),

                Filter::make('due_date_range')
                    ->label('Rentang Tenggat')
                    ->form([
                        DatePicker::make('due_from')->label('Dari'),
                        DatePicker::make('due_until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['due_from'],  fn ($q, $v) => $q->whereDate('due_date', '>=', $v))
                            ->when($data['due_until'], fn ($q, $v) => $q->whereDate('due_date', '<=', $v));
                    }),

                Filter::make('overdue')
                    ->label('Hanya Terlambat')
                    ->query(fn (Builder $q) => $q->overdue()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('cancel')
                        ->label('Batalkan Tugas Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan tugas terpilih?')
                        ->modalDescription('Status semua tugas yang dipilih akan diubah menjadi "Dibatalkan".')
                        ->action(function (Collection $records): void {
                            $records->each(fn ($r) => $r->update(['status' => 'cancelled']));
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
