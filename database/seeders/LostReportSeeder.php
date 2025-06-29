<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LostReport;
use App\Models\Borrowing;
use App\Models\AdminUser;
use App\Enum\LostReportStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LostReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LostReport::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $borrowings = Borrowing::with(['siteUser', 'bookCopy'])->latest()->limit(3)->get();

        $admin = AdminUser::first();

        if ($borrowings->count() < 3 || !$admin) {
            $this->command->warn('Seeding LostReport dibatalkan: Minimal perlu 3 data borrowing dan 1 admin user di database.');
            return;
        }

        $now = Carbon::now();

        $lostReportsData = [];

        // Contoh 1: Status "Reported" (Baru Dilaporkan)
        $borrowing1 = $borrowings->get(0);
        if ($borrowing1) {
            $lostReportsData[] = [
                'site_user_id' => $borrowing1->site_user_id,
                'book_copy_id' => $borrowing1->book_copy_id,
                'borrowing_id' => $borrowing1->id,
                'report_date' => $now->copy()->subHours(10)->toDateTimeString(),
                'status' => LostReportStatus::Reported,
                'admin_user_id_verify' => null,
                'resolution_notes' => null,
                'resolution_date' => null,
                'created_at' => $now->copy()->subHours(10),
                'updated_at' => $now->copy()->subHours(10),
            ];
        }

        // Contoh 2: Status "Verified" (Sudah dicek Admin)
        $borrowing2 = $borrowings->get(1);
        if ($borrowing2) {
            $lostReportsData[] = [
                'site_user_id' => $borrowing2->site_user_id,
                'book_copy_id' => $borrowing2->book_copy_id,
                'borrowing_id' => $borrowing2->id,
                'report_date' => $now->copy()->subDays(2)->setTime(15, 0)->toDateTimeString(),
                'status' => LostReportStatus::Verified,
                'admin_user_id_verify' => $admin->id,
                'resolution_notes' => null,
                'resolution_date' => null,
                'created_at' => $now->copy()->subDays(2)->setTime(15, 0),
                'updated_at' => $now->copy()->subDay()->setTime(9, 0),
            ];
        }


        // Contoh 3: Status "Resolved" (Sudah Selesai)
        $borrowing3 = $borrowings->get(2);
        if ($borrowing3) {
            $lostReportsData[] = [
                'site_user_id' => $borrowing3->site_user_id,
                'book_copy_id' => $borrowing3->book_copy_id,
                'borrowing_id' => $borrowing3->id,
                'report_date' => $now->copy()->subWeeks(1)->setTime(10, 0)->toDateTimeString(),
                'status' => LostReportStatus::Resolved,
                'admin_user_id_verify' => $admin->id,
                'resolution_notes' => 'Siswa bersedia mengganti buku yang hilang dengan judul yang sama. Denda biaya penggantian dibatalkan.',
                'resolution_date' => $now->copy()->subDays(1)->setTime(16, 0)->toDateTimeString(),
                'created_at' => $now->copy()->subWeeks(1)->setTime(10, 0),
                'updated_at' => $now->copy()->subDays(1)->setTime(16, 0),
            ];
        }


        if (!empty($lostReportsData)) {
            foreach ($lostReportsData as $data) {
                LostReport::firstOrCreate(
                    ['borrowing_id' => $data['borrowing_id']],
                    $data
                );
            }
            $this->command->info('Tabel Lost Reports berhasil diisi dengan data contoh.');
        } else {
            $this->command->info('Tidak ada data contoh Lost Report yang dibuat (mungkin data borrowing kurang?).');
        }
    }
}
