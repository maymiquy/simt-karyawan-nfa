<?php

namespace App\Filament\Resources\Assignments;

use App\Filament\Resources\Assignments\Pages\CreateAssignment;
use App\Filament\Resources\Assignments\Pages\EditAssignment;
use App\Filament\Resources\Assignments\Pages\ListAssignments;
use App\Filament\Resources\Assignments\Pages\ViewAssignment;
use App\Filament\Resources\Assignments\Schemas\AssignmentForm;
use App\Filament\Resources\Assignments\Schemas\AssignmentInfolist;
use App\Filament\Resources\Assignments\Tables\AssignmentsTable;
use App\Models\Assignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $navigationLabel = 'Penugasan';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AssignmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssignmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAssignments::route('/'),
            'create' => CreateAssignment::route('/create'),
            'view'   => ViewAssignment::route('/{record}'),
            'edit'   => EditAssignment::route('/{record}/edit'),
        ];
    }

    // Semua role bisa akses, tapi data yang tampil berbeda
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Admin', 'Manager', 'Employee']);
    }

    // Hanya Admin & Manager yang bisa assign task
    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Manager']);
    }

    // Employee hanya bisa edit progress miliknya sendiri
    public static function canEdit($record): bool
    {
        if (auth()->user()->hasRole('Employee')) {
            return $record->user_id === auth()->id();
        }
        return auth()->user()->hasAnyRole(['Admin', 'Manager']);
    }

    // Hanya Admin yang bisa hapus assignment
    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    // Filter data: Employee hanya lihat assignment miliknya
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->hasRole('Employee')) {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }
}