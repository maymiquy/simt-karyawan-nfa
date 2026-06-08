<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = User::role('Employee')
            ->withCount(['assignments as active_tasks_count' => function ($q) {
                $q->whereIn('progress', ['not_started', 'on_progress', 'submitted', 'revision']);
            }])
            ->orderBy('name')
            ->get();
        // KPI dihitung via accessor $employee->kpi_percent di view.

        return view('manager.employees.index', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('Employee');

        return redirect()->route('manager.employees.index')
            ->with('success', 'Akun karyawan berhasil dibuat.');
    }
}
