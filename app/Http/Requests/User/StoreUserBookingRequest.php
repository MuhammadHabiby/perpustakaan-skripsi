<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Enum\BookingStatus;
use App\Enum\BorrowingStatus;
use Illuminate\Validation\Validator;

class StoreUserBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $book = $this->route('book');

            if (!$user || !$book) {
                $validator->errors()->add('general', 'Data pengguna atau buku tidak valid.');
                return;
            }

            $maxBookings = (int) setting('max_active_bookings', 2);
            $currentActiveBookings = $user->bookings()->where('status', BookingStatus::Active)->count();
            if ($currentActiveBookings >= $maxBookings) {
                $validator->errors()->add('limit', "Anda sudah mencapai batas maksimal booking aktif ({$maxBookings} buku).");
            }

            $hasActiveBookingForThisBook = $user->bookings()
                ->where('book_id', $book->id)
                ->where('status', BookingStatus::Active)
                ->exists();
            if ($hasActiveBookingForThisBook) {
                $validator->errors()->add('duplicate_booking', 'Anda sudah memiliki booking aktif untuk buku ini.');
            }

            $hasActiveLoanForThisBook = $user->borrowings()
                ->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::Overdue])
                ->whereHas('bookCopy', function ($q) use ($book) {
                    $q->where('book_id', $book->id);
                })
                ->exists();
            if ($hasActiveLoanForThisBook) {
                $validator->errors()->add('duplicate_loan', 'Anda sedang meminjam buku ini, tidak bisa melakukan booking.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'general.required' => 'Data pengguna atau buku tidak valid.',
            'limit.required' => 'Anda sudah mencapai batas maksimal booking aktif.',
            'duplicate_booking.required' => 'Anda sudah memiliki booking aktif untuk buku ini.',
            'duplicate_loan.required' => 'Anda sedang meminjam buku ini, tidak bisa melakukan booking.',
        ];
    }
}
