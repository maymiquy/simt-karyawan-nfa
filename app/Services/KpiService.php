<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * KPI v2 — composite multi-dimensi berbasis periode bulanan.
 *
 *   KPI% = w.quality × Quality% + w.ontime × OnTime% + w.completion × Completion%
 *
 * - Quality%    : rata-rata mutu tugas (max(0, 10 − penalti×revisi)) berbobot prioritas, ÷10×100.
 * - OnTime%     : % tugas disetujui yang submit ≤ tenggat.
 * - Completion% : tugas disetujui ÷ tugas yang jatuh tempo pada periode (anti-gaming).
 *
 * Bobot, bobot prioritas, penalti, target & band diambil dari config/kpi.php.
 */
class KpiService
{
    /**
     * Ringkasan KPI seorang karyawan pada satu periode bulanan.
     *
     * @param  User             $user
     * @param  Carbon|null      $monthAnchor  Tanggal acuan periode (default: sekarang).
     * @param  bool             $withTrend    Hitung juga KPI bulan sebelumnya untuk tren.
     * @return array
     */
    public function summaryForUser(User $user, ?Carbon $monthAnchor = null, bool $withTrend = true): array
    {
        $anchor = $monthAnchor ? $monthAnchor->copy() : Carbon::now();
        $start  = $anchor->copy()->startOfMonth();
        $end    = $anchor->copy()->endOfMonth();

        $data = $this->computeForPeriod($user, $start, $end);

        $trend = ['previous' => null, 'delta' => null];
        if ($withTrend) {
            $prevAnchor = $anchor->copy()->subMonthNoOverflow();
            $prev = $this->computeForPeriod(
                $user,
                $prevAnchor->copy()->startOfMonth(),
                $prevAnchor->copy()->endOfMonth()
            );
            $trend['previous'] = $prev['percent'];
            if ($data['percent'] !== null && $prev['percent'] !== null) {
                $trend['delta'] = $data['percent'] - $prev['percent'];
            }
        }

        $target = (int) config('kpi.target', 85);

        return [
            // ── ringkasan utama ──
            'percent'         => $data['percent'],
            'target'          => $target,
            'band'            => self::band($data['percent'])['key'],
            'band_label'      => self::band($data['percent'])['label'],
            'period'          => $anchor->format('Y-m'),
            'period_label'    => $anchor->translatedFormat('F Y'),
            'breakdown'       => [
                'quality'    => $data['quality'],
                'ontime'     => $data['ontime'],
                'completion' => $data['completion'],
            ],
            'trend'           => $trend,
            // ── hitungan pendukung ──
            'approved'        => $data['approved'],
            'due_in_period'   => $data['due'],
            'on_time'         => $data['on_time'],
            'late'            => $data['late'],
            'total_revisions' => $data['revisions'],
        ];
    }

    /**
     * Inti perhitungan komposit untuk rentang [start, end] (berdasarkan tenggat tugas).
     */
    private function computeForPeriod(User $user, Carbon $start, Carbon $end): array
    {
        $dueInPeriod = Assignment::with('task')
            ->where('user_id', $user->id)
            ->whereHas('task', fn ($q) => $q->whereBetween('due_date', [$start, $end]))
            ->get();

        $denom = $dueInPeriod->count();

        if ($denom === 0) {
            return [
                'percent' => null, 'quality' => null, 'ontime' => null, 'completion' => null,
                'approved' => 0, 'due' => 0, 'on_time' => 0, 'late' => 0, 'revisions' => 0,
            ];
        }

        $approved      = $dueInPeriod->where('progress', 'done');
        $approvedCount = $approved->count();

        $priorityWeight = config('kpi.priority_weight', ['high' => 1.5, 'medium' => 1.0, 'low' => 0.75]);
        $revPenalty     = (int) config('kpi.revision_penalty', 2);

        // Quality% — rata-rata mutu berbobot prioritas
        $wSum = 0.0;
        $wScore = 0.0;
        foreach ($approved as $a) {
            $mutu = max(0, 10 - $revPenalty * (int) $a->revision_count);
            $w    = $priorityWeight[$a->task->priority ?? 'medium'] ?? 1.0;
            $wScore += $mutu * $w;
            $wSum   += $w;
        }
        $quality = ($approvedCount > 0 && $wSum > 0)
            ? round($wScore / $wSum / 10 * 100, 1)
            : 0.0;

        // OnTime% — proporsi submit tepat waktu di antara yang disetujui
        $onTimeCount = $approved->filter(fn ($a) =>
            $a->submitted_at && $a->task?->due_date && $a->submitted_at->lte($a->task->due_date)
        )->count();
        $ontime = $approvedCount > 0 ? round($onTimeCount / $approvedCount * 100, 1) : 0.0;

        // Completion% — disetujui ÷ jatuh tempo pada periode
        $completion = round($approvedCount / $denom * 100, 1);

        // Komposit
        $w = config('kpi.weights', ['quality' => 0.5, 'ontime' => 0.3, 'completion' => 0.2]);
        $percent = (int) round(
            $w['quality'] * $quality + $w['ontime'] * $ontime + $w['completion'] * $completion
        );
        $percent = max(0, min(100, $percent));

        return [
            'percent'    => $percent,
            'quality'    => $quality,
            'ontime'     => $ontime,
            'completion' => $completion,
            'approved'   => $approvedCount,
            'due'        => $denom,
            'on_time'    => $onTimeCount,
            'late'       => $approvedCount - $onTimeCount,
            'revisions'  => (int) $approved->sum('revision_count'),
        ];
    }

    /**
     * KPI persen (periode berjalan) — tanpa tren agar ringan untuk daftar.
     */
    public function percentForUser(User $user): ?int
    {
        return $this->summaryForUser($user, null, false)['percent'];
    }

    /**
     * Pita warna untuk badge (mengikuti palet kpi-badge: green/amber/red/gray).
     */
    public static function color(?int $percent): string
    {
        if ($percent === null) {
            return 'gray';
        }

        $bands = config('kpi.bands', ['excellent' => 90, 'good' => 75, 'fair' => 60]);

        return match (true) {
            $percent >= $bands['good'] => 'green',
            $percent >= $bands['fair'] => 'amber',
            default                    => 'red',
        };
    }

    /**
     * Band 4-tingkat (excellent/good/fair/poor) untuk label detail.
     *
     * @return array{key:string,label:string}
     */
    public static function band(?int $percent): array
    {
        if ($percent === null) {
            return ['key' => 'none', 'label' => 'Belum ada data'];
        }

        $bands = config('kpi.bands', ['excellent' => 90, 'good' => 75, 'fair' => 60]);

        return match (true) {
            $percent >= $bands['excellent'] => ['key' => 'excellent', 'label' => 'Sangat Baik'],
            $percent >= $bands['good']      => ['key' => 'good',      'label' => 'Baik'],
            $percent >= $bands['fair']      => ['key' => 'fair',      'label' => 'Cukup'],
            default                         => ['key' => 'poor',      'label' => 'Perlu Perbaikan'],
        };
    }
}
