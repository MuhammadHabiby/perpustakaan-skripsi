<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingCancelledNotification extends Notification
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
        return [
            'message' => "Booking Anda untuk buku \"{$bookTitle}\" telah dibatalkan oleh admin.",
            'icon' => 'bi-journal-x',
            'link' => route('user.bookings.index'),
        ];
    }
}
