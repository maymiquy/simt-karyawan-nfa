<?php

namespace App\Filament\Resources\Assignments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AssignmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('task.title')
                    ->label('Tugas'),

                TextEntry::make('user.name')
                    ->label('Karyawan'),

                TextEntry::make('assignedBy.name')
                    ->label('Di-assign oleh'),

                TextEntry::make('progress')
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

                TextEntry::make('completion_notes')
                    ->label('Laporan Penyelesaian')
                    ->placeholder('—')
                    ->columnSpanFull(),

                TextEntry::make('manager_notes')
                    ->label('Catatan Manager')
                    ->placeholder('—')
                    ->columnSpanFull(),

                TextEntry::make('submitted_at')
                    ->label('Dikirim pada')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—'),

                TextEntry::make('reviewed_at')
                    ->label('Ditinjau pada')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—'),
            ]);
    }
}
