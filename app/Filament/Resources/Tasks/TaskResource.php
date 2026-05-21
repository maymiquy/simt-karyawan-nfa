<?php

namespace App\Filament\Resources\Tasks;

use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Filament\Resources\Tasks\Pages\ViewTask;
use App\Filament\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Resources\Tasks\Schemas\TaskInfolist;
use App\Filament\Resources\Tasks\Tables\TasksTable;
use App\Models\Task;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationLabel = 'Kelola Task';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaskInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'view'   => ViewTask::route('/{record}'),
            'edit'   => EditTask::route('/{record}/edit'),
        ];
    }

    // Admin & Manager bisa akses menu Task
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Admin', 'Manager']);
    }

    // Admin & Manager bisa buat task baru
    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Manager']);
    }

    // Admin & Manager bisa edit task
    public static function canEdit($record): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Manager']);
    }

    // Hanya Admin yang bisa hapus task
    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('Admin');
    }
}