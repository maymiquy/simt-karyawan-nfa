<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $assignments = Assignment::with('task')
            ->where('user_id', $user->id)
            ->get();

        $activeCount   = $assignments->where('progress', 'on_progress')->count();
        $doneCount     = $assignments->where('progress', 'done')->count();
        $totalCount    = $assignments->count();

        $nearDueCount = $assignments
            ->filter(fn ($a) =>
                $a->task &&
                $a->task->due_date &&
                $a->task->due_date->between(now(), now()->addDays(3)) &&
                ! in_array($a->progress, ['done'])
            )
            ->count();

        $completionRate = $totalCount > 0
            ? round(($doneCount / $totalCount) * 100)
            : 0;

        $recentAssignments = Assignment::with(['task', 'task.creator'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('employee.dashboard', compact(
            'activeCount',
            'doneCount',
            'nearDueCount',
            'totalCount',
            'completionRate',
            'recentAssignments',
        ));
    }
}
