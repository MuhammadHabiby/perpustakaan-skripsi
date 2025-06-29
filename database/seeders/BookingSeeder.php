<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\SiteUser;
use App\Models\Book;
use App\Models\Setting;
use App\Enum\BookingStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Booking::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = SiteUser::inRandomOrder()->limit(5)->get();
        $books = Book::inRandomOrder()->limit(10)->get();

        if ($users->count() < 2 || $books->count() < 5) {
            $this->command->warn('Seeding Booking dibatalkan: Minimal perlu 2 user aktif dan 5 buku di database.');
            return;
        }

        $bookingExpiryDays = (int) (Setting::where('key', 'booking_expiry_days')->value('value') ?? 2);

        $now = Carbon::now();

        $bookingsData = [
            // 1. Status: Active, Belum Expired
            [
                'site_user_id' => $users->get(0)->id,
                'book_id' => $books->get(0)->id,
                'booking_date' => $now->copy()->subDay()->setTime(10, 0, 0)->toDateTimeString(),
                'expiry_date' => $now->copy()->subDay()->addDays($bookingExpiryDays)->endOfDay()->toDateTimeString(),
                'status' => BookingStatus::Active,
                'notes' => 'Contoh booking yang masih aktif dan valid.',
                'created_at' => $now->copy()->subDay()->setTime(10, 0, 0),
                'updated_at' => $now->copy()->subDay()->setTime(10, 0, 0),
            ],
            // 2. Status: Active, tapi Sudah Lewat Expiry Date
            [
                'site_user_id' => $users->get(1)->id,
                'book_id' => $books->get(1)->id,
                'booking_date' => $now->copy()->subDays(3)->setTime(14, 0, 0)->toDateTimeString(),
                'expiry_date' => $now->copy()->subDays(3)->addDays($bookingExpiryDays)->endOfDay()->toDateTimeString(),
                'status' => BookingStatus::Active,
                'notes' => 'Booking aktif yang sudah lewat batas waktu pengambilan.',
                'created_at' => $now->copy()->subDays(3)->setTime(14, 0, 0),
                'updated_at' => $now->copy()->subDays(3)->setTime(14, 0, 0),
            ],
            // 3. Status: Active, Baru Dibuat
            [
                'site_user_id' => $users->get(0)->id,
                'book_id' => $books->get(2)->id,
                'booking_date' => $now->copy()->subHours(2)->toDateTimeString(),
                'expiry_date' => $now->copy()->subHours(2)->addDays($bookingExpiryDays)->endOfDay()->toDateTimeString(),
                'status' => BookingStatus::Active,
                'notes' => null,
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'site_user_id' => $users->get(1)->id,
                'book_id' => $books->get(3)->id,
                'booking_date' => $now->copy()->subDays(5)->setTime(9, 0, 0)->toDateTimeString(),
                'expiry_date' => $now->copy()->subDays(5)->addDays($bookingExpiryDays)->endOfDay()->toDateTimeString(),
                'status' => BookingStatus::Expired,
                'notes' => 'Booking otomatis kedaluwarsa oleh sistem.',
                'created_at' => $now->copy()->subDays(5)->setTime(9, 0, 0),
                'updated_at' => $now->copy()->subDays(3)->setTime(0, 1, 0),
            ],
            // 5. Status: ConvertedToLoan (sudah dikonversi)
            [
                'site_user_id' => $users->get(0)->id,
                'book_id' => $books->get(4)->id,
                'booking_date' => $now->copy()->subDays(4)->setTime(11, 0, 0)->toDateTimeString(),
                'expiry_date' => $now->copy()->subDays(4)->addDays($bookingExpiryDays)->endOfDay()->toDateTimeString(),
                'status' => BookingStatus::ConvertedToLoan,
                'notes' => 'Telah dikonversi menjadi peminjaman oleh Admin.',
                'created_at' => $now->copy()->subDays(4)->setTime(11, 0, 0),
                'updated_at' => $now->copy()->subDays(2)->setTime(15, 0, 0),
            ],
            // 6. Status: Cancelled (dibatalkan)
            [
                'site_user_id' => $users->get(1)->id,
                'book_id' => $books->get(0)->id,
                'booking_date' => $now->copy()->subHours(5)->toDateTimeString(),
                'expiry_date' => $now->copy()->subHours(5)->addDays($bookingExpiryDays)->endOfDay()->toDateTimeString(),
                'status' => BookingStatus::Cancelled,
                'notes' => 'Dibatalkan oleh admin: Stok buku fisik ternyata rusak.',
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(1),
            ],
        ];

        foreach ($bookingsData as $data) {
            Booking::create($data);
        }
    }
}
