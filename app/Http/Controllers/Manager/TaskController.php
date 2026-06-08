<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    private function baseQuery()
    {
        $isAdmin = Auth::user()->hasRole('Admin');
        return $isAdmin ? Task::query() : Task::where('created_by', Auth::id());
    }

    public function index(Request $request): View
    {
        $query = $this->baseQuery()->with(['creator', 'assignments.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('due_from')) {
            $query->whereDate('due_date', '>=', $request->due_from);
        }

        if ($request->filled('due_to')) {
            $query->whereDate('due_date', '<=', $request->due_to);
        }

        $tasks = $query->latest()->paginate(15)->withQueryString();

        return view('manager.tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $employees = User::role('Employee')->orderBy('name')->get();
        return view('manager.tasks.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date'    => ['nullable', 'date', 'after_or_equal:today'],
            'priority'    => ['required', 'in:low,medium,high'],
            'assignees'   => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
        ]);

        $task = Task::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date'    => $validated['due_date'] ?? null,
            'priority'    => $validated['priority'],
            'status'      => 'pending',
            'created_by'  => Auth::id(),
        ]);

        foreach ($validated['assignees'] ?? [] as $userId) {
            Assignment::create([
                'task_id'     => $task->id,
                'user_id'     => $userId,
                'assigned_by' => Auth::id(),
                'progress'    => 'not_started',
            ]);
        }

        return redirect()->route('manager.tasks.index')
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(int $id): View
    {
        $task = $this->baseQuery()
            ->with([
                'creator',
                'assignments.user',
                'assignments.assignedBy',
                'assignments.activities',
                'assignments.logs' => fn ($q) => $q->with('user')->oldest(),
            ])
            ->findOrFail($id);

        $employees = User::role('Employee')
            ->whereNotIn('id', $task->assignments->pluck('user_id'))
            ->orderBy('name')
            ->get();

        return view('manager.tasks.show', compact('task', 'employees'));
    }

    public function edit(int $id): View
    {
        $task      = $this->baseQuery()->findOrFail($id);
        $employees = User::role('Employee')->orderBy('name')->get();
        $assignedIds = $task->assignments()->pluck('user_id')->toArray();

        return view('manager.tasks.edit', compact('task', 'employees', 'assignedIds'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $task = $this->baseQuery()->findOrFail($id);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date'    => ['nullable', 'date'],
            'priority'    => ['required', 'in:low,medium,high'],
            'status'      => ['required', 'in:pending,in_progress,completed,overdue,cancelled'],
        ]);

        $task->update($validated);

        return redirect()->route('manager.tasks.show', $task->id)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $task = $this->baseQuery()->findOrFail($id);
        $task->delete();

        return redirect()->route('manager.tasks.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }
}
