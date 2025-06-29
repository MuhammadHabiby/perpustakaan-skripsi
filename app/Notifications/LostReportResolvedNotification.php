<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\LostReport;

class LostReportResolvedNotification extends Notification
{
    use Queueable;

    public LostReport $lostReport;

    public function __construct(LostReport $lostReport)
    {
        $this->lostReport = $lostReport;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $bookTitle = $this->lostReport->bookCopy?->book?->title ?? 'N/A';
        return [
            'message' => "Laporan kehilangan Anda untuk buku \"{$bookTitle}\" telah selesai diproses oleh admin.",
            'icon' => 'bi-check2-all',
            'link' => route('user.borrowings.history'),
        ];
    }
}
