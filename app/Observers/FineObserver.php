<?php

namespace App\Observers;

use App\Models\Fine;
use App\Enum\FineStatus;
use App\Models\SiteUser;
use App\Notifications\FineGeneratedNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FineObserver
{
    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function created(Fine $fine): void
    {
        if ($fine->status === FineStatus::Unpaid) {
            $user = $fine->borrowing?->siteUser;
            if ($user) {
                try {
                    $notification = new FineGeneratedNotification($fine);
                    $dbData = $notification->toDatabase($user);
                    $user->notifyNow($notification);
                    $this->sendFcmNotification(
                        $user,
                        'Denda Baru Diterbitkan',
                        $dbData['message'],
                        ['type' => 'fine_generated', 'fine_id' => (string) $fine->id, 'link' => $dbData['link'] ?? route('user.fines.index'), 'icon' => $dbData['icon'] ?? 'bi-cash-coin']
                    );
                } catch (\Exception $e) {
                    Log::error("Error sending FineGeneratedNotification for Fine ID {$fine->id}: " . $e->getMessage());
                }
            } else {
                Log::warning("Cannot send FineGeneratedNotification: User not found or inactive for Fine ID {$fine->id} via Borrowing ID {$fine->borrowing_id}.");
            }
        }
    }

    protected function sendFcmNotification(SiteUser $user, string $title, string $body, array $data = []): void
    {
        $fcmToken = $user->fcm_token;
        if ($fcmToken) {
            try {
                $link = $data['link'] ?? route('dashboard');
                unset($data['link']);
                $message = CloudMessage::withTarget('token', $fcmToken)
                    ->withNotification(FirebaseNotification::create($title, $body))
                    ->withData(array_merge($data, ['click_action' => $link]));
                $this->messaging->send($message);
            } catch (\Kreait\Firebase\Exception\MessagingException | \Kreait\Firebase\Exception\FirebaseException $e) {
                Log::error("FCM Send Error (Observer) for User ID {$user->id}: " . $e->getMessage());
            } catch (\Throwable $e) {
                Log::error("FCM Send Error (General Observer) for User ID {$user->id}: " . $e->getMessage());
            }
        }
    }
}
