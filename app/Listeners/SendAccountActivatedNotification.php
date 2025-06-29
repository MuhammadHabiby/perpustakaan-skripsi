<?php

namespace App\Listeners;

use App\Events\UserAccountActivated;
use App\Models\SiteUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\AccountActivatedNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class SendAccountActivatedNotification
{
    protected Messaging $messaging;
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function handle(UserAccountActivated $event): void
    {
        $user = $event->user;
        try {
            $notification = new AccountActivatedNotification();
            $dbData = $notification->toDatabase($user);
            $user->notifyNow($notification);
            $this->sendFcmNotification(
                $user,
                'Akun Perpustakaan Aktif!',
                $dbData['message'],
                ['type' => 'account_activated', 'link' => $dbData['link'] ?? route('login'), 'icon' => $dbData['icon'] ?? 'bi-person-check-fill']
            );
        } catch (\Exception $e) {
            Log::error("Failed sending AccountActivatedNotification for User ID {$user->id}: " . $e->getMessage());
        }
    }
    protected function sendFcmNotification(SiteUser $user, string $title, string $body, array $data = []): void
    {
        $fcmToken = $user->fcm_token;

        if ($fcmToken) {
            try {
                $message = CloudMessage::withTarget('token', $fcmToken)
                    ->withNotification(FirebaseNotification::create($title, $body))
                    ->withData(array_merge($data, ['click_action' => $data['link'] ?? route('dashboard')]));

                $this->messaging->send($message);
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                Log::error("FCM Send Error (MessagingException) for User ID {$user->id}: " . $e->getMessage());
            } catch (\Throwable $e) {
                Log::error("FCM Send Error (General) for User ID {$user->id}: " . $e->getMessage());
            }
        }
    }
}
