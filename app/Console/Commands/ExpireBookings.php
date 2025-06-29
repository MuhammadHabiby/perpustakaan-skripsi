<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Enum\BookingStatus;
use App\Enum\BookCopyStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';
    protected $description = 'Update status of expired bookings and release book copies';

    public function handle()
    {
        $this->info('Mencari booking yang sudah kadaluarsa...');

        $now = Carbon::now();
        $expiredBookings = Booking::where('status', BookingStatus::Active)
            ->where('expiry_date', '<', $now)
            ->with('bookCopy')
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('Tidak ada booking kadaluarsa yang ditemukan.');
            return 0;
        }

        $this->info("Ditemukan {$expiredBookings->count()} booking kadaluarsa. Memproses...");
        $processedCount = 0;

        foreach ($expiredBookings as $booking) {
            DB::beginTransaction();
            try {
                $booking->status = BookingStatus::Expired;
                $booking->notes = trim(($booking->notes ? $booking->notes . "\n\n---\n\n" : '') . "Otomatis kadaluarsa pada " . $now->isoFormat('D MMM YYYY, HH:mm'));
                $booking->save();

                if ($booking->bookCopy && $booking->bookCopy->status === BookCopyStatus::Booked) {
                    $booking->bookCopy->status = BookCopyStatus::Available;
                    $booking->bookCopy->save();
                }

                DB::commit();
                $processedCount++;
                $this->line("Booking ID {$booking->id} diupdate ke Expired.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to expire Booking ID {$booking->id}: " . $e->getMessage());
                $this->error("Gagal memproses Booking ID {$booking->id}. Cek log.");
            }
        }

        $this->info("Selesai. {$processedCount} booking berhasil diupdate statusnya menjadi Expired.");
        return 0;
    }
}
