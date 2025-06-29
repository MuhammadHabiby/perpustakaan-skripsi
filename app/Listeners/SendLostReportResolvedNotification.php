<?php

namespace App\Listeners;

use App\Events\LostReportResolved;
use App\Models\SiteUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\LostReportResolvedNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class SendLostReportResolvedNotification
{
    protected Messaging $messaging;
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function handle(LostReportResolved $event): void
    {
        $user = $event->lostReport->reporter;
        if ($user) {
            try {
                $notification = new LostReportResolvedNotification($event->lostReport);
                $dbData = $notification->toDatabase($user);
                $user->notifyNow($notification);
                $this->sendFcmNotification(
                    $user,
                    'Laporan Kehilangan Diproses',
                    $dbData['message'],
                    ['type' => 'lost_report_resolved', 'report_id' => (string) $event->lostReport->id, 'link' => $dbData['link'] ?? route('user.borrowings.history'), 'icon' => $dbData['icon'] ?? 'bi-check2-all']
                );
            } catch (\Exception $e) {
                Log::error("Failed sending LostReportResolvedNotification for Report ID {$event->lostReport->id}: " . $e->getMessage());
            }
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
