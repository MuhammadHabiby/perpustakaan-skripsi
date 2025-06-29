<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Enum\BookingStatus;

class CancelBookingRequest extends FormRequest
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
        return [
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_notes.string' => 'Catatan pembatalan harus berupa teks.',
            'admin_notes.max' => 'Catatan pembatalan terlalu panjang (maksimal 1000 karakter).',
        ];
    }
}
