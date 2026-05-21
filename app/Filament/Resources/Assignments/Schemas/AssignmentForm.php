<?php

namespace App\Filament\Resources\Assignments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('task_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_by')
                    ->required()
                    ->numeric(),
                Select::make('progress')
                    ->options([
            'not_started' => 'Not started',
            'on_progress' => 'On progress',
            'done' => 'Done',
            'revision' => 'Revision',
        ])
                    ->default('not_started')
                    ->required(),
                DateTimePicker::make('submitted_at'),
            ]);
    }
}
