<?php

namespace App\Listeners;

use App\Events\BookingConvertedToLoan;
use App\Models\SiteUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\BookingConvertedNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class SendBookingConvertedNotification
{
    protected Messaging $messaging;
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function handle(BookingConvertedToLoan $event): void
    {
        $user = $event->booking->siteUser;
        if ($user) {
            try {
                $notification = new BookingConvertedNotification($event->booking);
                $dbData = $notification->toDatabase($user);
                $user->notifyNow($notification);
                $this->sendFcmNotification(
                    $user,
                    'Booking Dikonfirmasi',
                    $dbData['message'],
                    ['type' => 'booking_converted', 'booking_id' => (string) $event->booking->id, 'link' => $dbData['link'] ?? route('user.borrowings.history'), 'icon' => $dbData['icon'] ?? 'bi-check2-square']
                );
            } catch (\Exception $e) {
                Log::error("Failed sending BookingConvertedNotification for Booking ID {$event->booking->id}: " . $e->getMessage());
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
