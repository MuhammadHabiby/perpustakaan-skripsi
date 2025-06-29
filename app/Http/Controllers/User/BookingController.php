<?php

namespace App\Http\Controllers\User;

use App\Enum\BookCopyStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Enum\BookingStatus;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Requests\User\StoreUserBookingRequest;
use App\Models\Book;
use App\Models\BookCopy;

class BookingController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $activeBookings = Booking::where('site_user_id', $user->id)
            ->where('status', BookingStatus::Active)
            ->with('book:id,slug,title,cover_image')
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->map(function ($booking) use ($today, $tomorrow) {
                $expiryDate = $booking->expiry_date ? Carbon::parse($booking->expiry_date)->endOfDay() : null;

                $booking->is_expired = $expiryDate && $expiryDate->lt($today);
                $booking->is_expiring_soon = !$booking->is_expired && $expiryDate && $expiryDate->lt($tomorrow->copy()->addDay());

                return $booking;
            });

        $pastBookings = Booking::where('site_user_id', $user->id)
            ->whereIn('status', [
                BookingStatus::Expired,
                BookingStatus::ConvertedToLoan,
                BookingStatus::Cancelled
            ])
            ->with('book:id,slug,title')
            ->latest('updated_at')
            ->paginate(10, ['*'], 'riwayat_page');

        return view('user.bookings.index', compact(
            'activeBookings',
            'pastBookings'
        ));
    }

    public function store(StoreUserBookingRequest $request, Book $book): RedirectResponse
    {

        $user = Auth::user();
        $bookCopy = null;

        DB::beginTransaction();
        try {
            $bookCopy = BookCopy::where('book_id', $book->id)
                ->where('status', BookCopyStatus::Available)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$bookCopy) {
                DB::rollBack();
                return redirect()->route('catalog.show', $book->slug)
                    ->with('error', 'Maaf, tidak ada eksemplar tersedia untuk buku ini saat ini.');
            }

            $bookCopy->status = BookCopyStatus::Booked;
            $bookCopy->save();


            $bookingExpiryDays = (int) setting('booking_expiry_days', 2);
            $now = Carbon::now();
            $expiryDate = $now->copy()->addDays($bookingExpiryDays)->endOfDay();

            Booking::create([
                'site_user_id' => $user->id,
                'book_id' => $book->id,
                'book_copy_id' => $bookCopy->id,
                'booking_date' => $now,
                'expiry_date' => $expiryDate,
                'status' => BookingStatus::Active,
                'notes' => 'Booking (' . $bookCopy->copy_code . ') oleh pengguna.',
            ]);

            DB::commit();

            return redirect()->route('user.bookings.index')
                ->with('success', 'Buku "' . $book->title . '" (Eksemplar: ' . $bookCopy->copy_code . ') berhasil dibooking! Segera ambil buku sebelum batas waktu: ' . $expiryDate->isoFormat('dddd, D MMMM YYYY HH:mm') . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating user booking (BookID: {$book->id}, UserID: {$user->id}) - " . $e->getMessage());
            return redirect()->route('catalog.show', $book->slug)
                ->with('error', 'Gagal melakukan booking. Terjadi kesalahan sistem.');
        }
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        if (Auth::id() !== $booking->site_user_id || $booking->status !== BookingStatus::Active) {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Anda tidak dapat membatalkan booking ini.');
        }

        DB::beginTransaction();
        try {
            $bookCopy = $booking->bookCopy;

            $booking->status = BookingStatus::Cancelled;
            $cancelNote = "Dibatalkan oleh pengguna pada " . now()->isoFormat('D MMM YYYY, HH:mm');
            $booking->notes = trim(($booking->notes ? $booking->notes . "\n\n---\n\n" : '') . $cancelNote);
            // Set book_copy_id jadi null setelah dibatalkan? Opsional, tapi bisa bantu tracking
            // $booking->book_copy_id = null;
            $booking->save();

            if ($bookCopy && $bookCopy->status === BookCopyStatus::Booked) {
                $bookCopy->status = BookCopyStatus::Available;
                $bookCopy->save();
            } elseif ($bookCopy && $bookCopy->status !== BookCopyStatus::Booked) {
                Log::warning("Booking ID {$booking->id} cancelled, but related BookCopy ID {$bookCopy->id} status was {$bookCopy->status->value}, not 'Booked'. Status not changed back.");
            }

            DB::commit();
            return redirect()->route('user.bookings.index')
                ->with('success', 'Booking untuk buku "' . $booking->book?->title . '"' . ($bookCopy ? ' (Eksemplar: ' . $bookCopy->copy_code . ')' : '') . ' berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error User cancelling Booking ID: {$booking->id} - " . $e->getMessage());
            return redirect()->route('user.bookings.index')
                ->with('error', 'Gagal membatalkan booking: Terjadi kesalahan sistem.');
        }
    }
}
