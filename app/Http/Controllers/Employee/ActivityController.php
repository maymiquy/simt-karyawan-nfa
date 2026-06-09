<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AssignmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Riwayat aktivitas lintas tugas milik karyawan, dengan filter tipe & tanggal.
     */
    public function index(Request $request): View
    {
        $query = AssignmentLog::with(['assignment.task', 'user'])
            ->whereHas('assignment', fn ($q) => $q->where('user_id', Auth::id()));

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        return view('employee.activity.index', compact('logs'));
    }
}
