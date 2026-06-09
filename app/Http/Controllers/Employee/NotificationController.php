<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Tandai 1 notifikasi terbaca lalu arahkan ke tugas terkait. */
    public function read(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $assignmentId = $notification->data['assignment_id'] ?? null;
        if ($assignmentId) {
            return redirect()->route('employee.tasks.show', $assignmentId);
        }

        return back();
    }

    /** Tandai semua notifikasi terbaca. */
    public function readAll(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi ditandai terbaca.');
    }

    /** Endpoint ringan untuk polling badge (jumlah belum terbaca). */
    public function json(): JsonResponse
    {
        return response()->json([
            'unread' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
