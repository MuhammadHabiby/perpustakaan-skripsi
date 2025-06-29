<?php

namespace App\Exports;

use App\Models\LostReport;
use App\Enum\LostReportStatus;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LostBookReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

        return LostReport::query()
            ->with([
                'reporter:id,nis,name',
                'bookCopy:id,copy_code,book_id',
                'bookCopy.book:id,title',
                'verifier:id,name'
            ])
            ->where('status', LostReportStatus::Resolved)
            ->whereBetween('resolution_date', [$start, $end])
            ->orderBy('resolution_date', 'asc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Laporan',
            'Tanggal Selesai',
            'Kode Eksemplar',
            'Judul Buku',
            'NIS Pelapor',
            'Nama Pelapor',
            'Admin Proses',
            'Catatan Penyelesaian',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->resolution_date ? $report->resolution_date->format('d-m-Y H:i') : '-',
            $report->bookCopy?->copy_code ?? 'N/A',
            $report->bookCopy?->book?->title ?? 'N/A',
            $report->reporter?->nis ?? 'N/A',
            $report->reporter?->name ?? 'N/A',
            $report->verifier?->name ?? 'N/A',
            $report->resolution_notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
