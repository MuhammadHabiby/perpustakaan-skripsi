<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteUser;
use Illuminate\Notifications\DatabaseNotification; 
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = SiteUser::first();

        if (!$user) {
            $this->command->warn('Tidak ada user untuk diberi notifikasi contoh.');
            return;
        }

        DatabaseNotification::where('notifiable_id', $user->id)
            ->where('notifiable_type', SiteUser::class)
            ->delete();

        $notifications = [
            [
                'id' => Str::uuid()->toString(),
                'type' => 'App\\Notifications\\ExampleNotification',
                'notifiable_type' => SiteUser::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['message' => 'Buku "Laskar Pelangi" akan jatuh tempo besok!', 'icon' => 'bi-calendar-event-fill', 'link' => '#']),
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'id' => Str::uuid()->toString(),
                'type' => 'App\\Notifications\\ExampleNotification',
                'notifiable_type' => SiteUser::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['message' => 'Booking Anda untuk "Bumi Manusia" telah dikonfirmasi.', 'icon' => 'bi-journal-check', 'link' => route('user.bookings.index')]),
                'read_at' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'type' => 'App\\Notifications\\ExampleNotification',
                'notifiable_type' => SiteUser::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['message' => 'Denda untuk peminjaman "Negeri 5 Menara" telah lunas.', 'icon' => 'bi-cash-stack']),
                'read_at' => now()->subHours(5),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
        ];

        DatabaseNotification::insert($notifications);

        $this->command->info('Notifikasi contoh berhasil ditambahkan untuk user ID: ' . $user->id);
    }
}
