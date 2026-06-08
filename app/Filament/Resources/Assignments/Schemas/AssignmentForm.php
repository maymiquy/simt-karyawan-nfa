<?php

namespace App\Filament\Resources\Assignments\Schemas;

use App\Models\Task;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('task_id')
                    ->label('Tugas')
                    ->relationship('task', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('assigned_by')
                    ->label('Di-assign oleh')
                    ->relationship('assignedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('progress')
                    ->label('Progress')
                    ->options([
                        'not_started' => 'Belum Mulai',
                        'on_progress' => 'Sedang Proses',
                        'submitted'   => 'Menunggu Review',
                        'done'        => 'Disetujui',
                        'revision'    => 'Perlu Revisi',
                    ])
                    ->default('not_started')
                    ->required(),

                Textarea::make('communication_note')
                    ->label('Catatan Komunikasi (Karyawan)')
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('manager_notes')
                    ->label('Catatan Manager')
                    ->rows(2)
                    ->columnSpanFull(),

                DateTimePicker::make('submitted_at')
                    ->label('Dikirim pada')
                    ->seconds(false),

                DateTimePicker::make('reviewed_at')
                    ->label('Ditinjau pada')
                    ->seconds(false),
            ]);
    }
}
