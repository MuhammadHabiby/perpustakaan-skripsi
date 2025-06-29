<?php

namespace App\Http\Controllers\Admin;

use App\Enum\FineStatus;
use App\Enum\LostReportStatus;
use App\Exports\BorrowingReportExport;
use App\Exports\FineReportExport;
use App\Exports\LostBookReportExport;
use App\Exports\ProcurementReportExport;
use App\Http\Controllers\Controller;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\LostReport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function borrowingReport(Request $request): View
    {
        $defaultDate = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', $defaultDate);
        $endDate = $request->input('end_date', $defaultDate);

        $validator = Validator::make(
            ['start_date' => $startDate, 'end_date' => $endDate],
            [
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            ],
            [
                'start_date.required' => 'Tanggal Mulai wajib diisi.',
                'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
                'end_date.required' => 'Tanggal Selesai wajib diisi.',
                'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
                'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
            ]
        );

        $borrowings = collect();

        if ($validator->passes()) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

                $borrowings = Borrowing::with([
                    'siteUser:id,nis,name',
                    'bookCopy:id,copy_code,book_id',
                    'bookCopy.book:id,title',
                ])
                    ->whereBetween('borrow_date', [$start, $end])
                    ->orderBy('borrow_date', 'asc')
                    ->get();
            } catch (\Exception $e) {
                Log::error("Error fetching borrowing report: " . $e->getMessage());
                return view('admin.reports.borrowings', compact('borrowings', 'startDate', 'endDate'))
                    ->withErrors($validator) // Kirim juga validator (meski lolos, antisipasi error lain)
                    ->with('error', 'Terjadi kesalahan saat mengambil data laporan.');
            }
        }

        return view('admin.reports.borrowings', compact('borrowings', 'startDate', 'endDate'))
            ->withErrors($validator);
    }

    public function exportBorrowingsExcel(Request $request): BinaryFileResponse | RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ], [
            'start_date.required' => 'Tanggal Mulai wajib diisi untuk export.',
            'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
            'end_date.required' => 'Tanggal Selesai wajib diisi untuk export.',
            'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
            'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.reports.borrowings')
                ->withErrors($validator)
                ->withInput($request->only(['start_date', 'end_date']));
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $fileName = 'laporan-peminjaman-' . $startDate . '-sd-' . $endDate . '.xlsx';

        return Excel::download(new BorrowingReportExport($startDate, $endDate), $fileName);
    }

    public function procurementReport(Request $request): View
    {
        $defaultDate = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', $defaultDate);
        $endDate = $request->input('end_date', $defaultDate);

        $validator = Validator::make(
            ['start_date' => $startDate, 'end_date' => $endDate],
            [
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            ],
            [
                'start_date.required' => 'Tanggal Mulai wajib diisi.',
                'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
                'end_date.required' => 'Tanggal Selesai wajib diisi.',
                'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
                'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
            ]
        );

        $bookCopies = collect();

        if ($validator->passes()) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

                $bookCopies = BookCopy::with([
                    'book:id,title,isbn,location,author_id,publisher_id',
                    'book.author:id,name',
                    'book.publisher:id,name',
                ])
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('created_at', 'asc')
                    ->get();
            } catch (\Exception $e) {
                Log::error("Error fetching procurement report: " . $e->getMessage());
                return view('admin.reports.procurements', compact('bookCopies', 'startDate', 'endDate'))
                    ->withErrors($validator)
                    ->with('error', 'Terjadi kesalahan saat mengambil data laporan.');
            }
        }

        return view('admin.reports.procurements', compact('bookCopies', 'startDate', 'endDate'))
            ->withErrors($validator);
    }

    public function exportProcurementsExcel(Request $request): BinaryFileResponse | RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ], [
            'start_date.required' => 'Tanggal Mulai wajib diisi untuk export.',
            'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
            'end_date.required' => 'Tanggal Selesai wajib diisi untuk export.',
            'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
            'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.reports.procurements')
                ->withErrors($validator)
                ->withInput($request->only(['start_date', 'end_date']));
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $fileName = 'laporan-pengadaan-' . $startDate . '-sd-' . $endDate . '.xlsx';

        return Excel::download(new ProcurementReportExport($startDate, $endDate), $fileName);
    }

    public function lostBookReport(Request $request): View
    {
        $defaultDate = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', $defaultDate);
        $endDate = $request->input('end_date', $defaultDate);

        $validator = Validator::make(
            ['start_date' => $startDate, 'end_date' => $endDate],
            [
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            ],
            [
                'start_date.required' => 'Tanggal Mulai wajib diisi.',
                'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
                'end_date.required' => 'Tanggal Selesai wajib diisi.',
                'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
                'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
            ]
        );

        $lostReports = collect();

        if ($validator->passes()) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

                $lostReports = LostReport::with([
                    'reporter:id,nis,name',
                    'bookCopy:id,copy_code,book_id',
                    'bookCopy.book:id,title',
                    'verifier:id,name'
                ])
                    ->where('status', LostReportStatus::Resolved)
                    ->whereBetween('resolution_date', [$start, $end])
                    ->orderBy('resolution_date', 'asc')
                    ->get();
            } catch (\Exception $e) {
                Log::error("Error fetching lost book report: " . $e->getMessage());
                return view('admin.reports.lost_books', compact('lostReports', 'startDate', 'endDate'))
                    ->withErrors($validator)
                    ->with('error', 'Terjadi kesalahan saat mengambil data laporan.');
            }
        }

        return view('admin.reports.lost_books', compact('lostReports', 'startDate', 'endDate'))
            ->withErrors($validator);
    }

    public function exportLostBooksExcel(Request $request): BinaryFileResponse | RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ], [
            'start_date.required' => 'Tanggal Mulai wajib diisi untuk export.',
            'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
            'end_date.required' => 'Tanggal Selesai wajib diisi untuk export.',
            'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
            'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.reports.lost-books')
                ->withErrors($validator)
                ->withInput($request->only(['start_date', 'end_date']));
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $fileName = 'laporan-buku-hilang-' . $startDate . '-sd-' . $endDate . '.xlsx';

        return Excel::download(new LostBookReportExport($startDate, $endDate), $fileName);
    }

    public function fineReport(Request $request): View
    {
        $defaultStartDate = Carbon::today()->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();
        $defaultStatus = '';

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $statusFilter = $request->input('status', $defaultStatus);

        $validStatuses = FineStatus::cases();
        $filterOptions = array_reduce($validStatuses, function ($opts, $status) {
            $opts[$status->value] = $status->label();
            return $opts;
        }, [
            '' => '-- Semua Status --',
            'settled' => 'Lunas/Dibebaskan'
        ]);


        $validator = Validator::make(
            ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $statusFilter],
            [
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'status' => ['nullable', 'string', function ($attribute, $value, $fail) use ($filterOptions) {
                    if ($value && !array_key_exists($value, $filterOptions)) {
                        $fail('Filter status tidak valid.');
                    }
                }],
            ],
            [
                'start_date.required' => 'Tanggal Mulai wajib diisi.',
                'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
                'end_date.required' => 'Tanggal Selesai wajib diisi.',
                'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
                'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
            ]
        );

        $fines = collect();
        $dateColumn = 'created_at';

        if ($validator->passes()) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

                $query = Fine::with([
                    'borrowing:id,site_user_id,book_copy_id',
                    'borrowing.siteUser:id,nis,name',
                    'borrowing.bookCopy:id,copy_code,book_id',
                    'borrowing.bookCopy.book:id,title',
                    'paymentProcessor:id,name'
                ]);

                if ($statusFilter === 'settled') {
                    $query->whereIn('status', [FineStatus::Paid, FineStatus::Waived]);
                    $query->whereBetween('payment_date', [$start, $end]);
                    $dateColumn = 'payment_date';
                } elseif ($statusFilter && FineStatus::tryFrom($statusFilter)) {
                    $statusEnum = FineStatus::from($statusFilter);
                    $query->where('status', $statusEnum);
                    if ($statusEnum === FineStatus::Paid || $statusEnum === FineStatus::Waived) {
                        $query->whereBetween('payment_date', [$start, $end]);
                        $dateColumn = 'payment_date';
                    } else { // Jika status Unpaid
                        $query->whereBetween('created_at', [$start, $end]);
                        $dateColumn = 'created_at';
                    }
                } else {
                    $query->whereBetween('created_at', [$start, $end]);
                    $dateColumn = 'created_at';
                }

                $fines = $query->orderBy($dateColumn, 'desc')->get();
            } catch (\Exception $e) {
                Log::error("Error fetching fine report: " . $e->getMessage());
                return view('admin.reports.fines', compact('fines', 'startDate', 'endDate', 'statusFilter', 'filterOptions'))
                    ->withErrors($validator)
                    ->with('error', 'Terjadi kesalahan saat mengambil data laporan.');
            }
        }

        return view('admin.reports.fines', compact('fines', 'startDate', 'endDate', 'statusFilter', 'filterOptions', 'dateColumn'))
            ->withErrors($validator);
    }

    public function exportFinesExcel(Request $request): BinaryFileResponse | RedirectResponse
    {
        $validStatuses = FineStatus::cases();
        $filterOptions = array_reduce($validStatuses, function ($opts, $status) {
            $opts[$status->value] = $status->label();
            return $opts;
        }, ['' => '-- Semua Status --', 'settled' => 'Lunas/Dibebaskan']);

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'status' => ['nullable', 'string', function ($attribute, $value, $fail) use ($filterOptions) {
                if ($value && !array_key_exists($value, $filterOptions)) {
                    $fail('Filter status tidak valid untuk export.');
                }
            }],
        ], [
            'start_date.required' => 'Tanggal Mulai wajib diisi untuk export.',
            'start_date.date_format' => 'Format Tanggal Mulai salah (YYYY-MM-DD).',
            'end_date.required' => 'Tanggal Selesai wajib diisi untuk export.',
            'end_date.date_format' => 'Format Tanggal Selesai salah (YYYY-MM-DD).',
            'end_date.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.reports.fines')
                ->withErrors($validator)
                ->withInput($request->only(['start_date', 'end_date', 'status']));
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $statusFilter = $request->input('status');

        $fileName = 'laporan-denda-' . $startDate . '-sd-' . $endDate . ($statusFilter ? '-' . str_replace(' ', '_', $statusFilter) : '-semua') . '.xlsx';

        return Excel::download(new FineReportExport($startDate, $endDate, $statusFilter), $fileName);
    }
}
