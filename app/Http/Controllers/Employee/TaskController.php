<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Assignment::with(['task', 'task.creator'])
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('progress', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('task', fn ($q) => $q->where('title', 'like', "%{$search}%"));
        }

        $assignments = $query->latest()->paginate(10)->withQueryString();

        return view('employee.tasks.index', compact('assignments'));
    }

    public function show(int $id): View
    {
        $assignment = Assignment::with(['task', 'task.creator', 'task.assignments.user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('employee.tasks.show', compact('assignment'));
    }

    public function updateProgress(Request $request, int $id): RedirectResponse
    {
        $assignment = Assignment::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'progress' => ['required', 'in:on_progress'],
        ]);

        $assignment->update(['progress' => 'on_progress']);

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function submitReport(Request $request, int $id): RedirectResponse
    {
        $assignment = Assignment::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'completion_notes' => ['required', 'string', 'min:20'],
        ]);

        $assignment->update([
            'progress'         => 'done',
            'completion_notes' => $request->completion_notes,
            'submitted_at'     => now(),
        ]);

        return back()->with('success', 'Laporan penyelesaian berhasil dikirim.');
    }
}
