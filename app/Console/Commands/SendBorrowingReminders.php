<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use App\Enum\BorrowingStatus;
use App\Notifications\BorrowingDueSoonNotification;
use App\Notifications\BorrowingOverdueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SiteUser;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class SendBorrowingReminders extends Command
{
    protected $signature = 'app:send-borrowing-reminders';
    protected $description = 'Kirim notifikasi jatuh tempo/lewat tempo & update status overdue';

    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        parent::__construct();
        $this->messaging = $messaging;
    }


    public function handle()
    {
        $this->info('Memulai pengecekan peminjaman untuk notifikasi...');
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $notificationSentCount = 0;
        $overdueUpdatedCount = 0;

        $this->line('Mencari peminjaman yang jatuh tempo besok...');
        $dueSoonBorrowings = Borrowing::where('status', BorrowingStatus::Borrowed)
            ->whereDate('due_date', $tomorrow)
            ->with('siteUser', 'bookCopy.book')
            ->cursor();

        foreach ($dueSoonBorrowings as $borrowing) {
            if ($borrowing->siteUser) {
                $this->line("- Memproses Borrowing ID: {$borrowing->id} untuk User ID: {$borrowing->siteUser->id} (Jatuh Tempo Besok)");
                try {
                    $notification = new BorrowingDueSoonNotification($borrowing);
                    $borrowing->siteUser->notify($notification);

                    $this->sendFcmNotification(
                        $borrowing->siteUser,
                        'Pengingat Jatuh Tempo',
                        $notification->toDatabase($borrowing->siteUser)['message'],
                        ['type' => 'due_soon', 'borrowing_id' => (string) $borrowing->id, 'link' => route('user.borrowings.history')]
                    );
                    $notificationSentCount++;
                } catch (\Exception $e) {
                    Log::error("Failed processing DueSoon for Borrowing ID {$borrowing->id}: " . $e->getMessage());
                    $this->error(" Gagal proses DueSoon Borrowing ID {$borrowing->id}.");
                }
            }
        }
        $this->info("Notifikasi jatuh tempo besok selesai diproses.");


        $this->line('Mencari peminjaman yang baru lewat tempo...');
        $newlyOverdueBorrowings = Borrowing::where('status', BorrowingStatus::Borrowed)
            ->whereDate('due_date', '<', $today)
            ->with('siteUser', 'bookCopy.book')
            ->cursor();

        foreach ($newlyOverdueBorrowings as $borrowing) {
            if ($borrowing->siteUser) {
                $this->line("- Memproses Borrowing ID: {$borrowing->id} untuk User ID: {$borrowing->siteUser->id} (Baru Overdue)");
                DB::beginTransaction();
                try {
                    $borrowing->status = BorrowingStatus::Overdue;
                    $borrowing->save();
                    $overdueUpdatedCount++;

                    $notification = new BorrowingOverdueNotification($borrowing);
                    $borrowing->siteUser->notify($notification);

                    $this->sendFcmNotification(
                        $borrowing->siteUser,
                        'Peminjaman Lewat Tempo!',
                        $notification->toDatabase($borrowing->siteUser)['message'],
                        ['type' => 'overdue', 'borrowing_id' => (string) $borrowing->id, 'link' => route('user.borrowings.history')]
                    );
                    $notificationSentCount++;

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed processing Overdue for Borrowing ID {$borrowing->id}: " . $e->getMessage());
                    $this->error(" Gagal proses Overdue Borrowing ID {$borrowing->id}.");
                }
            }
        }
        $this->info("Proses update status & notifikasi lewat tempo selesai.");
        $this->info("Total notifikasi terkirim: {$notificationSentCount}. Total status diupdate ke Overdue: {$overdueUpdatedCount}.");

        return Command::SUCCESS;
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
                $this->error(" FCM Error (User: {$user->id}): " . $e->getMessage());
            } catch (\Throwable $e) {
                Log::error("FCM Send Error (General) for User ID {$user->id}: " . $e->getMessage());
                $this->error(" FCM Error (User: {$user->id}): " . $e->getMessage());
            }
        } else {
            $this->line(" - FCM skipped for User ID: {$user->id} (no token)");
        }
    }
}
