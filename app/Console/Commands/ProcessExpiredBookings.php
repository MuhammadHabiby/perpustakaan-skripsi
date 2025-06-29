<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SiteUser;
use App\Enum\BookingStatus;
use App\Notifications\BookingExpiredNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class ProcessExpiredBookings extends Command
{
    protected $signature = 'app:process-expired-bookings';
    protected $description = 'Update status booking yang sudah melewati tanggal kadaluarsa dan kirim notifikasi';

    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        parent::__construct();
        $this->messaging = $messaging;
    }

    public function handle()
    {
        $this->info('Memulai proses update booking kadaluarsa...');
        $now = Carbon::now();
        $expiredBookings = Booking::where('status', BookingStatus::Active)
            ->where('expiry_date', '<', $now)
            ->with(['siteUser', 'book:id,title'])
            ->cursor();

        if (!$expiredBookings->valid()) {
            $this->info('Tidak ada booking aktif yang kadaluarsa.');
            return Command::SUCCESS;
        }

        $this->info("Memproses booking kadaluarsa...");
        $processedCount = 0;
        $notifiedCount = 0;

        foreach ($expiredBookings as $booking) {
            $user = $booking->siteUser;
            if (!$user) continue;

            $this->line("- Memproses Booking ID: {$booking->id} untuk User ID: {$user->id}");
            DB::beginTransaction();
            try {
                $booking->status = BookingStatus::Expired;
                $booking->notes = trim(($booking->notes ? $booking->notes . "\n\n---\n\n" : '') . "Otomatis diubah ke Expired pada " . $now->isoFormat('D MMM<y_bin_46>, HH:mm'));
                $booking->save();

                $notification = new BookingExpiredNotification($booking);
                $dbData = $notification->toDatabase($user);
                $user->notifyNow($notification);
                $this->sendFcmNotification(
                    $user,
                    'Booking Kadaluarsa',
                    $dbData['message'],
                    [
                        'type' => 'booking_expired',
                        'booking_id' => (string) $booking->id,
                        'link' => $dbData['link'] ?? route('user.bookings.index'),
                        'icon' => $dbData['icon'] ?? 'bi-calendar-x'
                    ]
                );
                $notifiedCount++;

                DB::commit();
                $processedCount++;
                $this->line("  -> Booking ID {$booking->id} diupdate ke Expired & notif dikirim.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to expire Booking ID {$booking->id}: " . $e->getMessage());
                $this->error("  Gagal memproses Booking ID {$booking->id}.");
            }
        }

        $this->info("Selesai. {$processedCount} booking diupdate. {$notifiedCount} notifikasi dikirim.");
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
