<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Carbon\Carbon;

class BookingExpiredNotification extends Notification
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $bookTitle = $this->booking->book?->title ?? 'N/A';
        $expiryDate = Carbon::parse($this->booking->expiry_date)->isoFormat('D MMM YY, HH:mm');
        return [
            'message' => "Sayang sekali, booking Anda untuk buku \"{$bookTitle}\" telah kadaluarsa pada {$expiryDate}.",
            'icon' => 'bi-calendar-x',
            'link' => route('user.bookings.index'),
            'booking_id' => $this->booking->id,
        ];
    }
}
