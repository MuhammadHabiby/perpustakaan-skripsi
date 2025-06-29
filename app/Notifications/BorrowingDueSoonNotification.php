<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Borrowing;
use Carbon\Carbon;

class BorrowingDueSoonNotification extends Notification
{
    use Queueable;

    public Borrowing $borrowing;

    public function __construct(Borrowing $borrowing)
    {
        $this->borrowing = $borrowing;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $bookTitle = $this->borrowing->bookCopy?->book?->title ?? 'N/A';
        $dueDate = Carbon::parse($this->borrowing->due_date)->isoFormat('dddd, D MMMM YYYY');
        return [
            'message' => "Pengingat: Buku \"{$bookTitle}\" akan jatuh tempo pada {$dueDate}.",
            'icon' => 'bi-calendar-event-fill',
            'link' => route('user.borrowings.history'),
        ];
    }
}
