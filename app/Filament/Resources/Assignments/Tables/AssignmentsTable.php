<?php

namespace App\Filament\Resources\Assignments\Tables;

use App\Models\AssignmentLog;
use App\Notifications\EmployeeAlert;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

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
                        'submitted'   => 'primary',
                        'done'        => 'success',
                        'revision'    => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'not_started' => 'Belum Mulai',
                        'on_progress' => 'Sedang Proses',
                        'submitted'   => 'Menunggu Review',
                        'done'        => 'Disetujui',
                        'revision'    => 'Perlu Revisi',
                        default       => $state,
                    }),

                TextColumn::make('kpi_score')
                    ->label('KPI')
                    ->badge()
                    ->placeholder('—')
                    ->color(fn ($state) => $state === null ? 'gray' : ($state >= 8 ? 'success' : ($state >= 5 ? 'warning' : 'danger')))
                    ->formatStateUsing(fn ($state) => $state === null ? '—' : rtrim(rtrim(number_format($state, 1), '0'), '.') . '/10'),

                TextColumn::make('revision_count')
                    ->label('Revisi')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->toggleable(),

                TextColumn::make('submitted_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('communication_note')
                    ->label('Catatan Karyawan')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                        'submitted'   => 'Menunggu Review',
                        'done'        => 'Disetujui',
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
                    ->modalDescription('KPI akan dihitung otomatis berdasarkan ketepatan waktu & jumlah revisi.')
                    ->visible(fn ($record) => $record->progress === 'submitted')
                    ->action(function ($record): void {
                        $kpi = $record->computeKpiScore();

                        DB::transaction(function () use ($record, $kpi) {
                            $record->update([
                                'progress'    => 'done',
                                'reviewed_at' => now(),
                                'kpi_score'   => $kpi,
                            ]);

                            AssignmentLog::create([
                                'assignment_id' => $record->id,
                                'user_id'       => auth()->id(),
                                'type'          => 'approved',
                                'meta'          => ['kpi' => $kpi, 'late' => $record->isLate()],
                            ]);
                        });

                        Notification::make()
                            ->title('Laporan disetujui')
                            ->body("KPI tugas ini: {$kpi}/10")
                            ->success()
                            ->send();
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
                    ->visible(fn ($record) => $record->progress === 'submitted')
                    ->action(function ($record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'progress'       => 'revision',
                                'manager_notes'  => $data['manager_notes'],
                                'reviewed_at'    => now(),
                                'revision_count' => $record->revision_count + 1,
                            ]);

                            AssignmentLog::create([
                                'assignment_id' => $record->id,
                                'user_id'       => auth()->id(),
                                'type'          => 'revised',
                                'notes'         => $data['manager_notes'],
                                'meta'          => ['revision_no' => $record->revision_count],
                            ]);

                            $record->user?->notify(new EmployeeAlert(
                                type: 'revision',
                                title: 'Tugas diminta revisi',
                                message: "Tugas \"{$record->task?->title}\" perlu direvisi.",
                                assignmentId: $record->id,
                            ));
                        });

                        Notification::make()
                            ->title('Tugas dikembalikan untuk revisi')
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
