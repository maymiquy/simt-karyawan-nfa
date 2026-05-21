<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return [
                Stat::make('Total User', User::count())
                    ->description('Semua user terdaftar')
                    ->color('primary'),

                Stat::make('Total Task', Task::count())
                    ->description('Semua task di sistem')
                    ->color('info'),

                Stat::make('Task Selesai', Task::where('status', 'completed')->count())
                    ->description('Task yang sudah selesai')
                    ->color('success'),

                Stat::make('Task Pending', Task::where('status', 'pending')->count())
                    ->description('Menunggu dikerjakan')
                    ->color('warning'),
            ];
        }

        if ($user->hasRole('Manager')) {
            return [
                Stat::make('Total Task', Task::count())
                    ->description('Semua task yang ada')
                    ->color('info'),

                Stat::make('Sudah Di-assign', Assignment::count())
                    ->description('Task yang sudah ditugaskan')
                    ->color('primary'),

                Stat::make('Selesai', Assignment::where('progress', 'done')->count())
                    ->description('Assignment selesai')
                    ->color('success'),
            ];
        }

        // Employee
        return [
            Stat::make('Task Saya', Assignment::where('user_id', $user->id)->count())
                ->description('Total tugas yang diberikan')
                ->color('primary'),

            Stat::make('Selesai', Assignment::where('user_id', $user->id)
                ->where('progress', 'done')->count())
                ->description('Tugas yang sudah selesai')
                ->color('success'),

            Stat::make('Dalam Proses', Assignment::where('user_id', $user->id)
                ->where('progress', 'on_progress')->count())
                ->description('Sedang dikerjakan')
                ->color('warning'),
        ];
    }
}