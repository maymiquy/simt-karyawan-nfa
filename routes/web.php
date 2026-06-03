<?php

use App\Http\Controllers\Auth\LoginController;
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

// Manager routes (Fase 4)
Route::middleware(['auth', 'role:Admin|Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', fn () => view('manager.dashboard'))->name('dashboard');
});

// Employee routes (Fase 3)
Route::middleware(['auth', 'role:Employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', fn () => view('employee.dashboard'))->name('dashboard');
});
