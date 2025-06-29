<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Carbon\Carbon;

class BookingExpiringSoonNotification extends Notification
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
        $expiryDate = Carbon::parse($this->booking->expiry_date)->isoFormat('dddd, D MMMM H:mm');
        $timeLeft = Carbon::parse($this->booking->expiry_date)->diffForHumans(null, true);

        return [
            'message' => "Pengingat: Booking Anda untuk buku \"{$bookTitle}\" akan kadaluarsa dalam {$timeLeft} ({$expiryDate}). Segera ambil buku di perpustakaan.",
            'icon' => 'bi-clock-history',
            'link' => route('user.bookings.index'),
        ];
    }
}
