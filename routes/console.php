<?php

use App\Console\Commands\ExpireBookings;
use App\Console\Commands\ProcessExpiredBookings;
use App\Console\Commands\SendBookingReminders;
use App\Console\Commands\SendBorrowingReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(ExpireBookings::class)->hourly()
    ->name('bookings:expire');

Schedule::command(ProcessExpiredBookings::class)->hourly()
    ->name('process-expired-bookings')
    ->withoutOverlapping();

Schedule::command(SendBorrowingReminders::class)->dailyAt('08:00')
    ->name('send-borrowing-reminders')
    ->withoutOverlapping();

Schedule::command(SendBookingReminders::class)->dailyAt('09:00')
    ->name('send-booking-reminders')
    ->withoutOverlapping();
