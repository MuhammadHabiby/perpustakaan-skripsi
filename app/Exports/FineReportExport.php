<?php

namespace App\Exports;

use App\Models\Fine;
use App\Enum\FineStatus;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class FineReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $statusFilter;

    public function __construct(string $startDate, string $endDate, ?string $statusFilter)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->statusFilter = $statusFilter;
    }

    public function query()
    {
        $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

        $query = Fine::query()
            ->with([
                'borrowing:id,site_user_id,book_copy_id',
                'borrowing.siteUser:id,nis,name',
                'borrowing.bookCopy:id,copy_code,book_id',
                'borrowing.bookCopy.book:id,title',
                'paymentProcessor:id,name'
            ]);

        $dateColumn = 'created_at';

        if ($this->statusFilter === 'settled') {
            $query->whereIn('status', [FineStatus::Paid, FineStatus::Waived]);
            $query->whereBetween('payment_date', [$start, $end]);
            $dateColumn = 'payment_date';
        } elseif ($this->statusFilter && FineStatus::tryFrom($this->statusFilter)) {
            $statusEnum = FineStatus::from($this->statusFilter);
            $query->where('status', $statusEnum);
            if ($statusEnum === FineStatus::Paid || $statusEnum === FineStatus::Waived) {
                $query->whereBetween('payment_date', [$start, $end]);
                $dateColumn = 'payment_date';
            } else {
                $query->whereBetween('created_at', [$start, $end]);
                $dateColumn = 'created_at';
            }
        } else {
            $query->whereBetween('created_at', [$start, $end]);
            $dateColumn = 'created_at';
        }

        $query->orderBy($dateColumn, 'desc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Denda',
            'Tanggal Dibuat',
            'NIS Peminjam',
            'Nama Peminjam',
            'Judul Buku',
            'Kode Eksemplar',
            'Jumlah Denda (Rp)',
            'Jumlah Dibayar (Rp)',
            'Status',
            'Admin Proses Bayar/Bebas',
            'Tgl Proses Bayar/Bebas',
            'Catatan',
        ];
    }

    public function map($fine): array
    {
        return [
            $fine->id,
            $fine->created_at ? $fine->created_at->format('d-m-Y H:i') : '-',
            $fine->borrowing?->siteUser?->nis ?? 'N/A',
            $fine->borrowing?->siteUser?->name ?? 'N/A',
            $fine->borrowing?->bookCopy?->book?->title ?? 'N/A',
            $fine->borrowing?->bookCopy?->copy_code ?? 'N/A',
            number_format($fine->amount, 0, ',', '.'),
            number_format($fine->paid_amount, 0, ',', '.'),
            $fine->status ? $fine->status->label() : '-',
            $fine->paymentProcessor?->name ?? '-',
            $fine->payment_date ? $fine->payment_date->format('d-m-Y H:i') : '-',
            $fine->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('G')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H')->getNumberFormat()->setFormatCode('#,##0');
        return [1 => ['font' => ['bold' => true]],];
    }
}
