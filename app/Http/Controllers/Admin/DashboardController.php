<?php

namespace App\Http\Controllers\Admin;

use App\Enum\LostReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\SiteUser;
use App\Models\Borrowing;
use App\Enum\BorrowingStatus;
use App\Enum\FineStatus;
use App\Models\Fine;
use App\Models\LostReport;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBooks = Book::count();
        $totalCopies = BookCopy::count();
        $totalStudents = SiteUser::count();
        $activeBorrowingsCount = Borrowing::whereIn('status', [
            BorrowingStatus::Borrowed,
            BorrowingStatus::Overdue
        ])->count();


        $overdueBorrowingsCount = Borrowing::where('status', BorrowingStatus::Overdue)
            ->orWhere(function ($query) {
                $query->where('status', BorrowingStatus::Borrowed)
                    ->whereDate('due_date', '<', Carbon::today());
            })->count();

        $pendingLostReportsCount = LostReport::whereIn('status', [
            LostReportStatus::Reported,
            LostReportStatus::Verified
        ])->count();

        $totalUnpaidFines = Fine::where('status', FineStatus::Unpaid)->sum('amount');

        $recentBorrowings = Borrowing::with([
            'siteUser:id,name',
            'bookCopy.book:id,title'
        ])
            ->latest('borrow_date')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalCopies',
            'totalStudents',
            'activeBorrowingsCount',
            'overdueBorrowingsCount',
            'pendingLostReportsCount',
            'totalUnpaidFines',
            'recentBorrowings'
        ));
    }
}
