<?php

namespace App\Filament\Resources\Assignments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('task.title')
                    ->label('Tugas')
                    ->searchable()
                    ->limit(35)
                    ->weight('medium'),

                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'not_started' => 'gray',
                        'on_progress' => 'info',
                        'done'        => 'success',
                        'revision'    => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'not_started' => 'Belum Mulai',
                        'on_progress' => 'Sedang Proses',
                        'done'        => 'Selesai',
                        'revision'    => 'Perlu Revisi',
                        default       => $state,
                    }),

                TextColumn::make('submitted_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('reviewed_at')
                    ->label('Ditinjau')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('completion_notes')
                    ->label('Laporan')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('manager_notes')
                    ->label('Catatan Manager')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('assignedBy.name')
                    ->label('Di-assign oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('progress')
                    ->label('Progress')
                    ->options([
                        'not_started' => 'Belum Mulai',
                        'on_progress' => 'Sedang Proses',
                        'done'        => 'Selesai',
                        'revision'    => 'Perlu Revisi',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui laporan?')
                    ->visible(fn ($record) => $record->progress === 'done' && $record->reviewed_at === null)
                    ->action(function ($record): void {
                        $record->update([
                            'reviewed_at' => now(),
                        ]);
                    }),
                Action::make('revision')
                    ->label('Minta Revisi')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Textarea::make('manager_notes')
                            ->label('Catatan untuk karyawan')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn ($record) => in_array($record->progress, ['on_progress', 'done']))
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'progress'      => 'revision',
                            'manager_notes' => $data['manager_notes'],
                            'reviewed_at'   => now(),
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
