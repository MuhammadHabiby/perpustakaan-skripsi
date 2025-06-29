<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Fine;
use App\Enum\FineStatus;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $fines = Fine::whereHas('borrowing', function ($query) use ($user) {
            $query->where('site_user_id', $user->id);
        })
            ->with([
                'borrowing:id,borrow_date,return_date,book_copy_id',
                'borrowing.bookCopy:id,copy_code,book_id',
                'borrowing.bookCopy.book:id,slug,title',
            ])
            ->latest('created_at')
            ->paginate(15);

        $totalUnpaidFines = Fine::whereHas('borrowing', function ($query) use ($user) {
            $query->where('site_user_id', $user->id);
        })
            ->where('status', FineStatus::Unpaid)
            ->sum('amount');

        return view('user.fines.index', compact('fines', 'totalUnpaidFines'));
    }
}
