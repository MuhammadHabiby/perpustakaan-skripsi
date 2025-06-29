<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SiteUser;
use App\Enum\BookingStatus;
use App\Notifications\BookingExpiringSoonNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class SendBookingReminders extends Command
{
    protected $signature = 'app:send-booking-reminders';
    protected $description = 'Kirim notifikasi pengingat untuk booking yang akan segera kadaluarsa (DB + FCM)';

    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        parent::__construct();
        $this->messaging = $messaging;
    }

    public function handle()
    {
        $this->info('Memulai pengecekan booking yang akan kadaluarsa...');

        $now = Carbon::now();
        $reminderThreshold = Carbon::now()->addDay();

        $expiringBookings = Booking::where('status', BookingStatus::Active)
            ->where('expiry_date', '>', $now)
            ->where('expiry_date', '<=', $reminderThreshold)
            ->with('siteUser', 'book:id,title')
            ->cursor();

        $sentDbCount = 0;
        $sentFcmCount = 0;
        $processedCount = 0;

        foreach ($expiringBookings as $booking) {
            $processedCount++;
            $user = $booking->siteUser;
            if (!$user) continue;

            $this->line("- Memproses Booking ID: {$booking->id} untuk User ID: {$user->id}");

            $alreadySentToday = $user->notifications()
                ->where('type', BookingExpiringSoonNotification::class)
                ->whereJsonContains('data->booking_id', $booking->id)
                ->where('created_at', '>=', Carbon::today())
                ->exists();

            if ($alreadySentToday) {
                $this->line("  -> Notifikasi untuk booking ini sudah dikirim hari ini. Dilewati.");
                continue;
            }

            try {
                $dbNotification = new BookingExpiringSoonNotification($booking);
                $dbData = $dbNotification->toDatabase($user);
                if (!isset($dbData['booking_id'])) {
                    $dbData['booking_id'] = $booking->id;
                }
                $user->notifyNow($dbNotification);

                $this->line("  -> Notifikasi Database terkirim.");
                $sentDbCount++;

                $this->sendFcmNotification(
                    $user,
                    'Booking Akan Kadaluarsa',
                    $dbData['message'],
                    [
                        'type' => 'booking_expiring_soon',
                        'booking_id' => (string) $booking->id,
                        'link' => $dbData['link'] ?? route('user.bookings.index'),
                        'icon' => $dbData['icon'] ?? 'bi-clock-history'
                    ]
                );
                if ($user->fcm_token) {
                    $sentFcmCount++;
                }
            } catch (\Exception $e) {
                Log::error("Failed processing BookingExpiringSoon for Booking ID {$booking->id}: " . $e->getMessage());
                $this->error("  Gagal proses notif BookingExpiringSoon untuk Booking ID {$booking->id}.");
            }
        }

        if ($processedCount === 0) {
            $this->info('Tidak ada booking aktif yang ditemukan akan segera kadaluarsa.');
        } else {
            $this->info("Selesai. {$sentDbCount} notifikasi DB & {$sentFcmCount} percobaan FCM terkirim.");
        }

        return Command::SUCCESS;
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
                $this->line("  -> Notifikasi FCM terkirim ke User ID: {$user->id}");
            } catch (\Kreait\Firebase\Exception\MessagingException | \Kreait\Firebase\Exception\FirebaseException $e) {
                Log::error("FCM Send Error for User ID {$user->id} (Booking Expiring): " . $e->getMessage());
                $this->error("  FCM Error (User: {$user->id}): " . $e->getMessage());
            } catch (\Throwable $e) {
                Log::error("FCM Send Error (General) for User ID {$user->id} (Booking Expiring): " . $e->getMessage());
                $this->error("  FCM Error (General, User: {$user->id}): " . $e->getMessage());
            }
        } else {
            $this->line("  -> FCM skipped for User ID: {$user->id} (no token)");
        }
    }
}
