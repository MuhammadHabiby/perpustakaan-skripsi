<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingConvertedNotification extends Notification
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
        $copyCode = $this->booking->bookCopy?->copy_code ?? 'N/A';
        return [
            'message' => "Booking Anda untuk \"{$bookTitle}\" (Eksemplar: {$copyCode}) telah dikonfirmasi dan menjadi peminjaman.",
            'icon' => 'bi-check2-square',
            'link' => route('user.borrowings.history'),
        ];
    }
}
