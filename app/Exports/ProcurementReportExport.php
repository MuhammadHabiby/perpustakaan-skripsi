<?php

namespace App\Exports;

use App\Models\BookCopy;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ProcurementReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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

        return BookCopy::query()
            ->with([
                'book:id,title,isbn,location,author_id,publisher_id',
                'book.author:id,name',
                'book.publisher:id,name',
            ])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal Pengadaan',
            'Kode Eksemplar',
            'Judul Buku',
            'Pengarang',
            'Penerbit',
            'ISBN',
            'Lokasi Rak',
            'Kondisi',
        ];
    }

    public function map($copy): array
    {
        return [
            $copy->created_at ? $copy->created_at->format('d-m-Y H:i') : '-',
            $copy->copy_code,
            $copy->book?->title ?? 'N/A',
            $copy->book?->author?->name ?? 'N/A',
            $copy->book?->publisher?->name ?? 'N/A',
            $copy->book?->isbn ?? '-',
            $copy->book?->location ?? '-',
            $copy->condition ? $copy->condition->label() : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
