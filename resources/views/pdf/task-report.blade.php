<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Tugas — NF Academy</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: #fff; padding: 18px 24px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p  { font-size: 9px; color: #93c5fd; margin-top: 2px; }
        .meta { padding: 12px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        .meta span { font-size: 9px; color: #64748b; }
        .content { padding: 16px 24px; }
        .summary { display: flex; gap: 12px; margin-bottom: 16px; }
        .stat-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; }
        .stat-box .num { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .stat-box .lbl { font-size: 8px; color: #94a3b8; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #1e3a8a; color: #fff; font-size: 9px; font-weight: 600; padding: 7px 8px; text-align: left; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 8px; font-size: 9px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: 600; }
        .badge-pending     { background: #f1f5f9; color: #64748b; }
        .badge-in_progress { background: #dbeafe; color: #1d4ed8; }
        .badge-completed   { background: #dcfce7; color: #16a34a; }
        .badge-overdue     { background: #fee2e2; color: #dc2626; }
        .badge-cancelled   { background: #f1f5f9; color: #94a3b8; }
        .badge-high        { background: #fee2e2; color: #dc2626; }
        .badge-medium      { background: #fef3c7; color: #d97706; }
        .badge-low         { background: #dcfce7; color: #16a34a; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Tugas Karyawan — NF Academy</h1>
    <p>
        Digenerate: {{ $generatedAt->translatedFormat('d M Y, H:i') }}
        @if (!empty($filters['from']) || !empty($filters['to']))
            | Periode: {{ $filters['from'] ?? '—' }} s/d {{ $filters['to'] ?? '—' }}
        @endif
    </p>
</div>

<div class="meta">
    <span>Total Tugas: <strong>{{ $tasks->count() }}</strong></span>
    <span>Selesai: <strong>{{ $tasks->where('status','completed')->count() }}</strong></span>
    <span>Terlambat: <strong>{{ $tasks->where('status','overdue')->count() }}</strong></span>
    <span>Proses: <strong>{{ $tasks->where('status','in_progress')->count() }}</strong></span>
</div>

<div class="content">

    @php
        $statusLabels   = ['pending'=>'Pending','in_progress'=>'Proses','completed'=>'Selesai','overdue'=>'Terlambat','cancelled'=>'Dibatalkan'];
        $priorityLabels = ['high'=>'Tinggi','medium'=>'Sedang','low'=>'Rendah'];
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:26%">Judul Tugas</th>
                <th style="width:9%">Prioritas</th>
                <th style="width:10%">Status</th>
                <th style="width:10%">Tenggat</th>
                <th style="width:22%">Karyawan</th>
                <th style="width:12%">Dibuat Oleh</th>
                <th style="width:7%">Tgl Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tasks as $i => $task)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $task->title }}</td>
                <td>
                    <span class="badge badge-{{ $task->priority }}">
                        {{ $priorityLabels[$task->priority] ?? $task->priority }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $task->status }}">
                        {{ $statusLabels[$task->status] ?? $task->status }}
                    </span>
                </td>
                <td>{{ $task->due_date?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $task->assignments->pluck('user.name')->filter()->implode(', ') ?: '—' }}</td>
                <td>{{ $task->creator?->name ?? '—' }}</td>
                <td>{{ $task->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#94a3b8; padding: 20px;">
                    Tidak ada data yang sesuai filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ now()->year }} NF Academy — Sistem Informasi Manajemen Tugas
    </div>
</div>

</body>
</html>
