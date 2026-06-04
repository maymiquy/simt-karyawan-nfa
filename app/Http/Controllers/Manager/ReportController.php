<?php

namespace App\Http\Controllers\Manager;

use App\Exports\TaskReportExport;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    private function buildQuery(Request $request)
    {
        $isAdmin = Auth::user()->hasRole('Admin');
        $query   = $isAdmin ? Task::query() : Task::where('created_by', Auth::id());

        $query->with(['creator', 'assignments.user']);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('employee_id')) {
            $query->whereHas('assignments', fn ($q) => $q->where('user_id', $request->employee_id));
        }

        return $query;
    }

    public function index(Request $request): View
    {
        $tasks = $this->buildQuery($request)->latest()->paginate(20)->withQueryString();

        $totalTasks     = $this->buildQuery($request)->count();
        $completedOnTime = $this->buildQuery($request)
            ->where('status', 'completed')
            ->where(fn ($q) => $q->whereNull('due_date')->orWhereColumn('updated_at', '<=', 'due_date'))
            ->count();
        $overdueTasks   = $this->buildQuery($request)->where('status', 'overdue')->count();
        $inProgressTasks = $this->buildQuery($request)->where('status', 'in_progress')->count();

        $employees = User::role('Employee')->orderBy('name')->get();

        return view('manager.reports.index', compact(
            'tasks',
            'totalTasks',
            'completedOnTime',
            'overdueTasks',
            'inProgressTasks',
            'employees',
        ));
    }

    public function downloadPdf(Request $request): Response
    {
        $tasks = $this->buildQuery($request)->latest()->get();

        $pdf = Pdf::loadView('pdf.task-report', [
            'tasks'     => $tasks,
            'filters'   => $request->only(['from', 'to', 'status', 'employee_id']),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-tugas-' . now()->format('Ymd') . '.pdf');
    }

    public function downloadExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['from', 'to', 'status', 'employee_id']);

        return Excel::download(
            new TaskReportExport($filters, Auth::user()),
            'laporan-tugas-' . now()->format('Ymd') . '.xlsx'
        );
    }
}
