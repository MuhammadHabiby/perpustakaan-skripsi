<?php

namespace App\Http\Controllers\Admin;

use App\Events\BookingCancelledByAdmin;
use App\Events\BookingConvertedToLoan;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\Setting;
use App\Enum\BookingStatus;
use App\Enum\BorrowingStatus;
use App\Enum\BookCopyStatus;
use App\Http\Requests\Admin\CancelBookingRequest;
use App\Http\Requests\Admin\ConvertBookingRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['siteUser:id,nis,name', 'book:id,title'])
            ->latest('booking_date')
            ->get();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'siteUser:id,nis,name,class,major,email',
            'book:id,title,isbn,location,cover_image,author_id,publisher_id,category_id',
            'book.author:id,name',
            'book.publisher:id,name',
            'book.category:id,name'
        ]);

        $availableCopies = BookCopy::where('book_id', $booking->book_id)
            ->where('status', BookCopyStatus::Available)
            ->orderBy('copy_code')
            ->get(['id', 'copy_code']);

        return view('admin.bookings.show', compact('booking', 'availableCopies'));
    }

    public function cancel(CancelBookingRequest $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validated();
        $adminNotes = $validated['admin_notes'] ?? null;

        DB::beginTransaction();
        try {
            $bookCopy = $booking->bookCopy;

            $booking->status = BookingStatus::Cancelled;
            $cancelReason = "Dibatalkan oleh admin: " . Auth::guard('admin')->user()->name . " pada " . now()->isoFormat('D MM YYYY, HH:mm');
            if (!empty($adminNotes)) {
                $cancelReason .= "\nCatatan: " . trim($adminNotes);
            }
            $booking->notes = trim(($booking->notes ? $booking->notes . "\n\n---\n\n" : '') . $cancelReason);
            $booking->save();

            if ($bookCopy && $bookCopy->status === BookCopyStatus::Booked) {
                $bookCopy->status = BookCopyStatus::Available;
                $bookCopy->save();
                Log::info("BookCopy ID {$bookCopy->id} status set to Available due to Booking ID {$booking->id} cancellation by admin.");
            } elseif ($bookCopy && $bookCopy->status !== BookCopyStatus::Booked) {
                Log::warning("Booking ID {$booking->id} cancelled by admin, but related BookCopy ID {$bookCopy->id} status was {$bookCopy->status->value}, not 'Booked'. Status not changed back.");
            } else if (!$bookCopy && $booking->book_copy_id) {
                Log::warning("Booking ID {$booking->id} cancelled by admin, has book_copy_id {$booking->book_copy_id} but relation failed to load.");
            }

            DB::commit();
            event(new BookingCancelledByAdmin($booking));
            return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error admin cancelling Booking ID: {$booking->id} - " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan booking: Terjadi kesalahan sistem.');
        }
    }

    public function convert(ConvertBookingRequest $request, Booking $booking): RedirectResponse
    {
        $bookCopy = $booking->bookCopy;
        $student = $booking->siteUser;

        if (!$bookCopy) {
            return redirect()->back()->with('error', 'Eksemplar buku yang terkait dengan booking ini tidak ditemukan.');
        }
        if (!in_array($bookCopy->status, [BookCopyStatus::Booked, BookCopyStatus::Available])) {
            return redirect()->back()->with('error', 'Eksemplar buku ini sedang tidak dalam status bisa dipinjamkan (Status: ' . $bookCopy->status->label() . ').');
        }
        if (!$student) {
            return redirect()->back()->with('error', 'Siswa pemesan tidak ditemukan atau tidak aktif.');
        }

        $maxLoanBooks = (int) setting('max_loan_books', 2);
        $activeLoansCount = $student->borrowings()
            ->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::Overdue])
            ->count();
        if ($activeLoansCount >= $maxLoanBooks) {
            return redirect()->back()->with('error', "Siswa telah mencapai batas maksimal peminjaman ({$maxLoanBooks} buku).");
        }

        DB::beginTransaction();
        try {
            $borrowDate = Carbon::now()->startOfDay();
            $loanDuration = (int) setting('loan_duration', 7);
            $dueDate = $borrowDate->copy()->addDays($loanDuration);

            Borrowing::create([
                'site_user_id' => $student->id,
                'book_copy_id' => $bookCopy->id,
                'booking_id' => $booking->id,
                'admin_user_id_loan' => Auth::guard('admin')->id(),
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => BorrowingStatus::Borrowed,
            ]);

            $booking->status = BookingStatus::ConvertedToLoan;
            $conversionNote = "Dikonversi ke peminjaman oleh admin: " . Auth::guard('admin')->user()->name . " pada " . now()->isoFormat('D MM YYYY, HH:mm') . " (Eksemplar: {$bookCopy->copy_code})";
            $booking->notes = trim(($booking->notes ? $booking->notes . "\n\n---\n\n" : '') . $conversionNote);
            $booking->save();

            $bookCopy->status = BookCopyStatus::Borrowed;
            $bookCopy->save();

            DB::commit();
            event(new BookingConvertedToLoan($booking));

            return redirect()->route('admin.borrowings.index')
                ->with('success', 'Booking berhasil dikonversi menjadi peminjaman untuk eksemplar: ' . $bookCopy->copy_code);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error converting Booking ID: {$booking->id} - " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal konversi booking: Terjadi kesalahan sistem.');
        }
    }
}
