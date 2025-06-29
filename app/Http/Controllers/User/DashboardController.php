<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fine;
use App\Enum\BorrowingStatus;
use App\Enum\BookingStatus;
use App\Enum\FineStatus;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        $currentBorrowings = $user->borrowings()
            ->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::Overdue])
            ->with(['bookCopy.book:id,title,cover_image', 'bookCopy:id,book_id,copy_code'])
            ->latest('borrow_date')
            ->get();
        $activeBorrowingsCount = $currentBorrowings->count();

        $overdueBorrowingsCount = $currentBorrowings->filter(function ($borrowing) use ($today) {
            return $borrowing->due_date && Carbon::parse($borrowing->due_date)->startOfDay()->lt($today);
        })->count();

        $activeBookings = $user->bookings()
            ->where('status', BookingStatus::Active)
            ->with('book:id,title,cover_image')
            ->latest('booking_date')
            ->get();
        $activeBookingsCount = $activeBookings->count();

        $unpaidFinesAmount = Fine::whereHas('borrowing', function ($query) use ($user) {
            $query->where('site_user_id', $user->id);
        })
            ->where('status', FineStatus::Unpaid)
            ->sum('amount');

        return view('user.dashboard', compact(
            'user',
            'currentBorrowings',
            'activeBorrowingsCount',
            'overdueBorrowingsCount',
            'activeBookings',
            'activeBookingsCount',
            'unpaidFinesAmount'
        ));
    }
}
