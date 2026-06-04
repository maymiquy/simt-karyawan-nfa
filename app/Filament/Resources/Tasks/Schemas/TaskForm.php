<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Tugas')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'Sedang Proses',
                        'completed'   => 'Selesai',
                        'overdue'     => 'Terlambat',
                        'cancelled'   => 'Dibatalkan',
                    ])
                    ->default('pending')
                    ->required(),

                Select::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'high'   => 'Tinggi',
                        'medium' => 'Sedang',
                        'low'    => 'Rendah',
                    ])
                    ->default('medium')
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Tanggal Tenggat'),

                Select::make('created_by')
                    ->label('Dibuat oleh')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
