<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessReturnRequest;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\SiteUser;
use App\Models\BookCopy;
use App\Models\Setting;
use App\Http\Requests\Admin\StoreBorrowingRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enum\BookCopyStatus;
use App\Enum\BorrowingStatus;
use App\Enum\FineStatus;
use Illuminate\Support\Facades\Log;

class BorrowingController extends Controller
{
    public function index(): View
    {
        $borrowings = Borrowing::with([
            'siteUser:id,nis,name',
            'bookCopy:id,copy_code,book_id',
            'bookCopy.book:id,title',
            'loanProcessor:id,name',
            'returnProcessor:id,name'
        ])->latest('borrow_date')->get();

        return view('admin.borrowings.index', compact('borrowings'));
    }

    public function create(): View
    {
        $students = SiteUser::orderBy('name')->get(['id', 'name', 'nis']);
        $availableCopies = BookCopy::where('status', BookCopyStatus::Available)
            ->with('book:id,title')
            ->orderBy('copy_code')
            ->get(['id', 'copy_code', 'book_id']);

        $loanDuration = (int) (Setting::where('key', 'loan_duration')->value('value') ?? 7);

        return view('admin.borrowings.create', compact('students', 'availableCopies', 'loanDuration'));
    }

    public function store(StoreBorrowingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $studentId = $validated['site_user_id'];
        $bookCopyId = $validated['book_copy_id'];
        $borrowDate = Carbon::parse($validated['borrow_date'] ?? now())->startOfDay();

        $student = SiteUser::find($studentId);
        $bookCopy = BookCopy::find($bookCopyId);

        if (!$student) {
            return redirect()->back()->with('error', 'Siswa tidak ditemukan atau tidak aktif.')->withInput();
        }
        if (!$bookCopy || $bookCopy->status !== BookCopyStatus::Available) {
            return redirect()->back()->with('error', 'Eksemplar buku tidak ditemukan atau sedang tidak tersedia.')->withInput();
        }
        $maxLoanBooks = Setting::where('key', 'max_loan_books')->value('value') ?? 2;
        $activeLoansCount = $student->borrowings()
            ->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::Overdue])
            ->count();
        if ($activeLoansCount >= $maxLoanBooks) {
            return redirect()->back()->with('error', "Siswa telah mencapai batas maksimal peminjaman ({$maxLoanBooks} buku).")->withInput();
        }

        DB::beginTransaction();
        try {
            $loanDuration = Setting::where('key', 'loan_duration')->value('value') ?? 7;
            $dueDate = $borrowDate->copy()->addDays((int)$loanDuration);

            Borrowing::create([
                'site_user_id' => $student->id,
                'book_copy_id' => $bookCopy->id,
                'admin_user_id_loan' => Auth::guard('admin')->id(),
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => BorrowingStatus::Borrowed,
            ]);

            $bookCopy->status = BookCopyStatus::Borrowed;
            $bookCopy->save();

            DB::commit();

            return redirect()->route('admin.borrowings.index')
                ->with('success', 'Peminjaman buku berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mencatat peminjaman: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Borrowing $borrowing): View
    {
        $borrowing->load([
            'siteUser:id,nis,name,class,major',
            'bookCopy:id,copy_code,condition,book_id',
            'bookCopy.book:id,title,isbn,location',
            'loanProcessor:id,name',
            'returnProcessor:id,name',
            'fine'
        ]);
        return view('admin.borrowings.show', compact('borrowing'));
    }

    public function destroy(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status === BorrowingStatus::Borrowed || $borrowing->status === BorrowingStatus::Overdue) {
            return redirect()->route('admin.borrowings.index')
                ->with('error', 'Gagal menghapus! Peminjaman ini masih aktif (belum dikembalikan atau hilang).');
        }

        if ($borrowing->fine && $borrowing->fine->status === FineStatus::Unpaid) {
            return redirect()->route('admin.borrowings.index')
                ->with('error', 'Gagal menghapus! Masih ada denda yang belum lunas terkait peminjaman ini.');
        }

        DB::beginTransaction();
        try {
            if ($borrowing->fine) {
                $borrowing->fine->delete();
            }
            $borrowing->delete();

            DB::commit();

            return redirect()->route('admin.borrowings.index')
                ->with('success', 'Data riwayat peminjaman berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.borrowings.index')
                ->with('error', 'Gagal menghapus data peminjaman: ' . $e->getMessage());
        }
    }

    public function processReturn(ProcessReturnRequest $request, Borrowing $borrowing): RedirectResponse
    {
        $adminNotes = $request->input('return_notes');

        $returnDate = Carbon::now()->startOfDay();
        $dueDate = Carbon::parse($borrowing->due_date)->startOfDay();
        $fineAmount = 0;
        $fineCreated = false;
        $overdueDays = 0;
        $calculatedFine = 0;

        if ($returnDate->greaterThan($dueDate)) {
            $overdueDays = $returnDate->diffInDays($dueDate, true);
            $fineRate = (int) setting('fine_rate_per_day', 0);

            if ($fineRate > 0 && $overdueDays > 0) {
                $calculatedFine = $overdueDays * $fineRate;
                $maxFine = (int) setting('max_fine_amount', 0);

                if ($maxFine > 0 && $calculatedFine > $maxFine) {
                    $fineAmount = $maxFine;
                } else {
                    $fineAmount = $calculatedFine;
                }
            }
        }

        DB::beginTransaction();
        try {
            $borrowing->return_date = $returnDate->toDateString();
            $borrowing->status = BorrowingStatus::Returned;
            $borrowing->admin_user_id_return = Auth::guard('admin')->id();

            $borrowing->save();

            if ($borrowing->bookCopy) {
                $borrowing->bookCopy->status = BookCopyStatus::Available;
                $borrowing->bookCopy->save();
            } else {
                Log::warning("BookCopy not found for Borrowing ID: {$borrowing->id} during return process.");
            }

            if ($fineAmount > 0) {
                $fineNotes = "Denda keterlambatan {$overdueDays} hari.";
                if (isset($maxFine) && $maxFine > 0 && $calculatedFine > $maxFine) {
                    $fineNotes .= " (Jumlah asli: Rp " . number_format($calculatedFine, 0, ',', '.') . ", diterapkan batas maks: Rp " . number_format($maxFine, 0, ',', '.') . ")";
                }
                if (!empty($adminNotes)) {
                    $fineNotes .= "\n\nCatatan Admin: " . $adminNotes;
                }

                Fine::create([
                    'borrowing_id' => $borrowing->id,
                    'amount' => $fineAmount,
                    'status' => FineStatus::Unpaid,
                    'notes' => trim($fineNotes),
                ]);
                $fineCreated = true;
            }

            DB::commit();

            $successMessage = 'Buku berhasil dikembalikan.';
            if ($fineCreated) {
                $successMessage .= ' Denda keterlambatan sebesar Rp ' . number_format($fineAmount, 0, ',', '.') . ' telah dibuat.';
            }

            return redirect()->route('admin.borrowings.show', $borrowing)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("EXCEPTION during return process for Borrowing ID: {$borrowing->id} - " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Gagal memproses pengembalian: Terjadi kesalahan sistem.');
        }
    }

    public function overdueIndex(): View
    {
        $today = Carbon::today();

        $overdueBorrowings = Borrowing::with([
            'siteUser:id,nis,name',
            'bookCopy:id,copy_code,book_id',
            'bookCopy.book:id,title',
        ])
            ->where(function ($query) use ($today) {
                $query->where('status', BorrowingStatus::Overdue)
                    ->orWhere(function ($subQuery) use ($today) {
                        $subQuery->where('status', BorrowingStatus::Borrowed)
                            ->whereDate('due_date', '<', $today);
                    });
            })
            ->orderBy('due_date', 'asc')
            ->get();

        foreach ($overdueBorrowings as $borrowing) {
            $dueDate = Carbon::parse($borrowing->due_date)->startOfDay();
            $borrowing->days_overdue = $today->diffInDays($dueDate, true);
        }

        return view('admin.borrowings.overdue', compact('overdueBorrowings'));
    }
}
