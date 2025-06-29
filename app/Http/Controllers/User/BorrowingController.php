<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;
use App\Enum\BorrowingStatus;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\LostReport;
use App\Enum\LostReportStatus;
use App\Http\Requests\User\ReportLostBorrowingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BorrowingController extends Controller
{
    public function history(): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        $activeBorrowings = Borrowing::where('site_user_id', $user->id)
            ->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::Overdue])
            ->with([
                'bookCopy:id,copy_code,book_id',
                'bookCopy.book:id,slug,title,cover_image'
            ])
            ->withExists('lostReport')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($borrowing) use ($today) {
                $borrowing->is_overdue = $borrowing->due_date && Carbon::parse($borrowing->due_date)->startOfDay()->lt($today);
                return $borrowing;
            });

        $pastBorrowings = Borrowing::where('site_user_id', $user->id)
            ->whereIn('status', [BorrowingStatus::Returned, BorrowingStatus::Lost])
            ->with([
                'bookCopy:id,copy_code,book_id',
                'bookCopy.book:id,slug,title',
                'fine'
            ])
            ->orderBy('return_date', 'desc')
            ->paginate(10, ['*'], 'riwayat_page');

        return view('user.borrowings.history', compact(
            'activeBorrowings',
            'pastBorrowings'
        ));
    }

    public function reportLost(ReportLostBorrowingRequest $request, Borrowing $borrowing): RedirectResponse
    {
        if (!$borrowing->site_user_id || !$borrowing->book_copy_id) {
            Log::error("Attempted to report lost on Borrowing ID {$borrowing->id} with missing user or book copy relation.");
            return redirect()->route('user.borrowings.history')
                ->with('error', 'Data peminjaman tidak lengkap untuk dilaporkan hilang.');
        }

        DB::beginTransaction();
        try {
            LostReport::create([
                'site_user_id' => $borrowing->site_user_id,
                'book_copy_id' => $borrowing->book_copy_id,
                'borrowing_id' => $borrowing->id,
                'report_date' => now(),
                'status' => LostReportStatus::Reported,
            ]);

            DB::commit();

            return redirect()->route('user.borrowings.history')
                ->with('success', 'Laporan kehilangan untuk buku "' . ($borrowing->bookCopy?->book?->title ?? 'N/A') . '" telah berhasil dikirim ke petugas perpustakaan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating Lost Report for Borrowing ID {$borrowing->id} by User ID " . Auth::id() . " - " . $e->getMessage());
            return redirect()->route('user.borrowings.history')
                ->with('error', 'Gagal mengirim laporan kehilangan. Terjadi kesalahan sistem.');
        }
    }
}
