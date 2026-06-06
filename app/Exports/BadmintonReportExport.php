<?php

namespace App\Exports;

use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BadmintonReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    private Collection $data;
    private array $headings;

    public function __construct(
        private readonly string $type,
        private readonly Carbon $startDate,
        private readonly Carbon $endDate,
    ) {
        $service = app(ReportingService::class);
        $this->data = $service->getReportByType($this->type, $this->startDate, $this->endDate);
        $this->headings = $service->getHeadingsByType($this->type);
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @param array $row
     */
    public function map($row): array
    {
        return collect($row)->map(function ($value, $key) {
            // Format currency columns
            if (in_array($key, ['jumlah', 'total', 'total_belanja'])) {
                return 'Rp ' . number_format((int) $value, 0, ',', '.');
            }
            return $value;
        })->values()->toArray();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F1D36'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        $typeLabels = ReportingService::getReportTypes();

        return $typeLabels[$this->type] ?? 'Laporan';
    }
}
