<?php

namespace App\Filament\Pages;

use App\Exports\TaskReportExport;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.reports';
    protected static ?string $navigationLabel     = 'Laporan';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static ?int    $navigationSort   = 5;
    protected static ?string $title            = 'Laporan Tugas';

    public ?string $from        = null;
    public ?string $to          = null;
    public ?string $status      = null;
    public ?string $employee_id = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from')
                    ->label('Dari Tanggal'),

                DatePicker::make('to')
                    ->label('Sampai Tanggal'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'Sedang Proses',
                        'completed'   => 'Selesai',
                        'overdue'     => 'Terlambat',
                        'cancelled'   => 'Dibatalkan',
                    ])
                    ->placeholder('Semua Status'),

                Select::make('employee_id')
                    ->label('Karyawan')
                    ->options(User::role('Employee')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Semua Karyawan'),
            ])
            ->columns(4);
    }

    private function buildQuery()
    {
        $isAdmin = Auth::user()->hasRole('Admin');
        $query   = $isAdmin ? Task::query() : Task::where('created_by', Auth::id());

        $query->with(['creator', 'assignments.user']);

        if ($this->from)        $query->whereDate('created_at', '>=', $this->from);
        if ($this->to)          $query->whereDate('created_at', '<=', $this->to);
        if ($this->status)      $query->where('status', $this->status);
        if ($this->employee_id) $query->whereHas('assignments', fn ($q) => $q->where('user_id', $this->employee_id));

        return $query;
    }

    public function getTasks()
    {
        return $this->buildQuery()->latest()->paginate(20);
    }

    public function getSummary(): array
    {
        $q = $this->buildQuery();
        return [
            'total'       => (clone $q)->count(),
            'completed'   => (clone $q)->where('status', 'completed')->count(),
            'overdue'     => (clone $q)->where('status', 'overdue')->count(),
            'in_progress' => (clone $q)->where('status', 'in_progress')->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action('exportPdf'),

            Action::make('downloadExcel')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportExcel'),
        ];
    }

    public function filter(): void
    {
        $this->form->getState();
    }

    public function exportPdf(): Response
    {
        $tasks = $this->buildQuery()->latest()->get();

        $pdf = Pdf::loadView('pdf.task-report', [
            'tasks'       => $tasks,
            'filters'     => ['from' => $this->from, 'to' => $this->to, 'status' => $this->status],
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'laporan-tugas-' . now()->format('Ymd') . '.pdf'
        );
    }

    public function exportExcel(): Response
    {
        $filters = [
            'from'        => $this->from,
            'to'          => $this->to,
            'status'      => $this->status,
            'employee_id' => $this->employee_id,
        ];

        return Excel::download(
            new TaskReportExport($filters, Auth::user()),
            'laporan-tugas-' . now()->format('Ymd') . '.xlsx'
        );
    }
}
