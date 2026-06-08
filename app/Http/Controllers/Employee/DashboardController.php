<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentLog;
use App\Services\KpiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(KpiService $kpiService): View
    {
        $user = Auth::user();

        $assignments = Assignment::with('task')
            ->where('user_id', $user->id)
            ->get();

        $activeCount = $assignments->whereIn('progress', ['on_progress', 'submitted', 'revision'])->count();
        $doneCount   = $assignments->where('progress', 'done')->count();
        $totalCount  = $assignments->count();

        $nearDueCount = $assignments
            ->filter(fn ($a) =>
                $a->task &&
                $a->task->due_date &&
                $a->task->due_date->between(now(), now()->addDays(3)) &&
                $a->progress !== 'done'
            )
            ->count();

        $completionRate = $totalCount > 0
            ? round(($doneCount / $totalCount) * 100)
            : 0;

        // KPI ringkasan
        $kpi = $kpiService->summaryForUser($user);

        $recentAssignments = Assignment::with(['task', 'task.creator'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Feed aktivitas pribadi (timeline lintas tugas)
        $recentLogs = AssignmentLog::with(['assignment.task', 'user'])
            ->whereHas('assignment', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->take(8)
            ->get();

        return view('employee.dashboard', compact(
            'activeCount',
            'doneCount',
            'nearDueCount',
            'totalCount',
            'completionRate',
            'kpi',
            'recentAssignments',
            'recentLogs',
        ));
    }
}
