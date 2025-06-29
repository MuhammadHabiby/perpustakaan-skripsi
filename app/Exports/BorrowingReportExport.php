<?php

namespace App\Exports;

use App\Models\Borrowing;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class BorrowingReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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

        return Borrowing::query()
            ->with([ // Eager load sama seperti di controller
                'siteUser:id,nis,name',
                'bookCopy:id,copy_code,book_id',
                'bookCopy.book:id,title',
            ])
            ->whereBetween('borrow_date', [$start, $end])
            ->orderBy('borrow_date', 'asc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Pinjam',
            'NIS Peminjam',
            'Nama Peminjam',
            'Judul Buku',
            'Kode Eksemplar',
            'Tanggal Pinjam',
            'Tanggal Jatuh Tempo',
            'Tanggal Kembali',
            'Status',
        ];
    }

    public function map($borrowing): array
    {
        return [
            $borrowing->id,
            $borrowing->siteUser?->nis ?? 'N/A',
            $borrowing->siteUser?->name ?? 'N/A',
            $borrowing->bookCopy?->book?->title ?? 'N/A',
            $borrowing->bookCopy?->copy_code ?? 'N/A',
            $borrowing->borrow_date ? $borrowing->borrow_date->format('d-m-Y') : '-',
            $borrowing->due_date ? $borrowing->due_date->format('d-m-Y') : '-',
            $borrowing->return_date ? $borrowing->return_date->format('d-m-Y') : '-',
            $borrowing->status ? $borrowing->status->label() : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
