<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $managerId = Auth::id();
        $isAdmin   = Auth::user()->hasRole('Admin');

        $taskQuery = $isAdmin
            ? Task::query()
            : Task::where('created_by', $managerId);

        $totalTasks     = (clone $taskQuery)->count();
        $inProgressTasks = (clone $taskQuery)->where('status', 'in_progress')->count();
        $completedMonth = (clone $taskQuery)
            ->where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->count();
        $overdueTasks   = (clone $taskQuery)->overdue()->count();

        // Active tasks for table
        $activeTasks = (clone $taskQuery)
            ->with(['assignments.user'])
            ->active()
            ->latest()
            ->take(10)
            ->get();

        // Chart: completed vs overdue per day (last 7 days)
        $chartData = collect(range(6, 0))->map(function ($daysAgo) use ($taskQuery, $isAdmin, $managerId) {
            $date = now()->subDays($daysAgo)->toDateString();
            $base = $isAdmin ? Task::query() : Task::where('created_by', $managerId);

            return [
                'date'      => now()->subDays($daysAgo)->translatedFormat('d M'),
                'completed' => (clone $base)->where('status', 'completed')->whereDate('updated_at', $date)->count(),
                'overdue'   => (clone $base)->overdue()->whereDate('due_date', $date)->count(),
            ];
        });

        // Feed aktivitas informatif (timeline lifecycle) untuk tugas milik manager/admin.
        $recentLogs = AssignmentLog::with(['user', 'assignment.user', 'assignment.task'])
            ->when(! $isAdmin, fn ($q) => $q->whereHas('assignment.task', fn ($t) => $t->where('created_by', $managerId)))
            ->latest()
            ->take(12)
            ->get();

        return view('manager.dashboard', compact(
            'totalTasks',
            'inProgressTasks',
            'completedMonth',
            'overdueTasks',
            'activeTasks',
            'chartData',
            'recentLogs',
        ));
    }
}
