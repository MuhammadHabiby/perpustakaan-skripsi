<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\BookCopy;
use App\Enum\BookingStatus;
use App\Enum\BookCopyStatus;

class ConvertBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!Auth::guard('admin')->check()) {
            return false;
        }
        $booking = $this->route('booking');
        return $booking instanceof Booking && $booking->status === BookingStatus::Active;
    }

    public function rules(): array
    {
        return [];
    }
}
