<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\User;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Halaman Log Aktivitas: accordion per karyawan -> per tugas -> timeline.
     * Satu halaman, tanpa pindah halaman (Alpine accordion).
     */
    public function index(Request $request, KpiService $kpiService): View
    {
        $isAdmin   = Auth::user()->hasRole('Admin');
        $managerId = Auth::id();

        // Semua assignment relevan (tugas milik manager, atau semua untuk admin).
        $assignments = Assignment::with([
                'task', 'user',
                'activities',
                'logs' => fn ($q) => $q->with('user')->oldest(),
            ])
            ->when(! $isAdmin, fn ($q) => $q->whereHas('task', fn ($t) => $t->where('created_by', $managerId)))
            ->when($request->filled('employee'), fn ($q) => $q->where('user_id', $request->employee))
            ->latest()
            ->get()
            ->groupBy('user_id');

        // Karyawan yang punya assignment relevan.
        $employees = User::role('Employee')
            ->whereIn('id', $assignments->keys())
            ->orderBy('name')
            ->get()
            ->map(function ($emp) use ($kpiService) {
                $emp->kpi = $kpiService->summaryForUser($emp, null, false);
                return $emp;
            });

        return view('manager.activity.index', compact('assignments', 'employees'));
    }
}
