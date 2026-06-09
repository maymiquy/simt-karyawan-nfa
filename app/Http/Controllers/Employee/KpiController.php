<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Services\KpiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KpiController extends Controller
{
    /**
     * Detail KPI pribadi: ringkasan + tren 6 bulan + daftar tugas beserta skor & alasan.
     */
    public function index(KpiService $kpiService): View
    {
        $user = Auth::user();

        $summary = $kpiService->summaryForUser($user);

        // Tren 6 bulan terakhir (termasuk bulan ini)
        $trend = collect(range(5, 0))->map(function ($back) use ($kpiService, $user) {
            $anchor = now()->subMonthsNoOverflow($back);
            $s = $kpiService->summaryForUser($user, $anchor, false);
            return [
                'label'   => $anchor->translatedFormat('M Y'),
                'percent' => $s['percent'],
            ];
        })->values();

        // Tugas disetujui yang jatuh tempo bulan ini (kontributor KPI)
        $tasks = Assignment::with('task')
            ->where('user_id', $user->id)
            ->where('progress', 'done')
            ->whereHas('task', fn ($q) => $q->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()]))
            ->get()
            ->map(function ($a) {
                $revPenalty = (int) config('kpi.revision_penalty', 2);
                $quality = max(0, 10 - $revPenalty * (int) $a->revision_count);
                $onTime  = $a->submitted_at && $a->task?->due_date && $a->submitted_at->lte($a->task->due_date);

                $reasons = [];
                if (! $onTime)               { $reasons[] = 'Terlambat'; }
                if ($a->revision_count > 0)  { $reasons[] = "{$a->revision_count}× revisi (−" . ($revPenalty * $a->revision_count) . ' mutu)'; }
                if (empty($reasons))         { $reasons[] = 'Tepat waktu, tanpa revisi'; }

                $a->kpi_quality = $quality;
                $a->kpi_ontime  = $onTime;
                $a->kpi_reason  = implode(' · ', $reasons);
                return $a;
            });

        return view('employee.kpi.index', compact('summary', 'trend', 'tasks'));
    }
}
