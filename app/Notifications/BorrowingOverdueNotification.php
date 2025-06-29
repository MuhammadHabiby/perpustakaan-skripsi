<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Borrowing;
use Carbon\Carbon;

class BorrowingOverdueNotification extends Notification
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
        $dueDate = Carbon::parse($this->borrowing->due_date)->isoFormat('D MMMM YYYY');
        $overdueDays = Carbon::today()->diffInDays(Carbon::parse($this->borrowing->due_date)->startOfDay(), true); // Hitung hari terlambat
        return [
            'message' => "Peringatan: Pengembalian buku \"{$bookTitle}\" sudah lewat {$overdueDays} hari dari jatuh tempo ({$dueDate}). Segera kembalikan untuk menghindari denda lebih lanjut.",
            'icon' => 'bi-calendar-x-fill',
            'link' => route('user.borrowings.history'),
        ];
    }
}
