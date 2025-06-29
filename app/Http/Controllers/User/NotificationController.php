<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);

        return view('user.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $notificationId): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
        }

        return back()->with('error', 'Notifikasi tidak ditemukan.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->route('user.notifications.index')
            ->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
