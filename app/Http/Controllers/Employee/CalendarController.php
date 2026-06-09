<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Kalender tenggat bulanan: menandai tugas berdasarkan due_date.
     */
    public function index(Request $request): View
    {
        try {
            $month = $request->filled('month')
                ? Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()
                : Carbon::now()->startOfMonth();
        } catch (\Exception $e) {
            $month = Carbon::now()->startOfMonth();
        }

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $assignments = Assignment::with('task')
            ->where('user_id', Auth::id())
            ->whereHas('task', fn ($q) => $q->whereBetween('due_date', [$start, $end]))
            ->get();

        // Kelompokkan per tanggal (1..31)
        $byDay = $assignments->groupBy(fn ($a) => (int) $a->task->due_date->day);

        return view('employee.calendar.index', compact('month', 'byDay'));
    }
}
