<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SiteUser;

class AccountActivatedNotification extends Notification
{
    use Queueable;

    public function __construct(/*SiteUser $user*/)
    {
        // $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'Akun perpustakaan Anda telah berhasil diaktifkan! Anda sekarang bisa login.',
            'icon' => 'bi-person-check-fill',
            'link' => route('login'),
        ];
    }
}
