<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Fine;

class FineGeneratedNotification extends Notification
{
    use Queueable;

    public Fine $fine;

    public function __construct(Fine $fine)
    {
        $this->fine = $fine;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $bookTitle = $this->fine->borrowing?->bookCopy?->book?->title ?? 'N/A';
        $amount = number_format($this->fine->amount, 0, ',', '.');
        return [
            'message' => "Denda baru sebesar Rp {$amount} telah dibuat untuk peminjaman buku \"{$bookTitle}\".",
            'icon' => 'bi-cash-coin',
            'link' => route('user.fines.index'),
        ];
    }
}
