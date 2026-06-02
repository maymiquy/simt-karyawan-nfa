<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Task;
use App\Observers\AssignmentObserver;
use App\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        Assignment::observe(AssignmentObserver::class);
    }
}
