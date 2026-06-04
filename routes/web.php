<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\TaskController as EmployeeTaskController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\TaskController as ManagerTaskController;
use App\Http\Controllers\Manager\AssignmentController as ManagerAssignmentController;
use App\Http\Controllers\Manager\EmployeeController as ManagerEmployeeController;
use App\Http\Controllers\Manager\ReportController as ManagerReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root: redirect berdasarkan status login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('Admin'))   return redirect('/admin');
        if ($user->hasRole('Manager')) return redirect()->route('manager.dashboard');
        return redirect()->route('employee.dashboard');
    }
    return redirect()->route('login');
});

// Auth
Route::get('/login',  [LoginController::class, 'show'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');
// Fallback GET logout: handle expired session / langsung klik link logout
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth');

// Manager routes (Fase 4)
Route::middleware(['auth', 'role:Admin|Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');

    // Tasks CRUD
    Route::get('/tasks',                [ManagerTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create',         [ManagerTaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks',               [ManagerTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}',           [ManagerTaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{id}/edit',      [ManagerTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{id}',           [ManagerTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{id}',        [ManagerTaskController::class, 'destroy'])->name('tasks.destroy');

    // Assignments
    Route::post('/tasks/{taskId}/assign',          [ManagerAssignmentController::class, 'store'])->name('assignments.store');
    Route::patch('/assignments/{id}/review',       [ManagerAssignmentController::class, 'review'])->name('assignments.review');
    Route::delete('/assignments/{id}',             [ManagerAssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Employees
    Route::get('/employees',  [ManagerEmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [ManagerEmployeeController::class, 'store'])->name('employees.store');

    // Reports
    Route::get('/reports',                [ManagerReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download/pdf',   [ManagerReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('/reports/download/excel', [ManagerReportController::class, 'downloadExcel'])->name('reports.excel');
});

// Employee routes (Fase 3)
Route::middleware(['auth', 'role:Employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard',                               [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tasks',                                   [EmployeeTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{id}',                              [EmployeeTaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{id}/progress',                   [EmployeeTaskController::class, 'updateProgress'])->name('tasks.progress');
    Route::post('/tasks/{id}/submit',                      [EmployeeTaskController::class, 'submitReport'])->name('tasks.submit');
});
