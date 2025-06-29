<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LostReport;
use App\Models\Fine;
use App\Enum\LostReportStatus;
use App\Enum\BorrowingStatus;
use App\Enum\BookCopyStatus;
use App\Enum\FineStatus;
use App\Events\LostReportResolved;
use App\Http\Requests\Admin\VerifyLostReportRequest;
use App\Http\Requests\Admin\ResolveLostReportRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LostReportController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $validStatuses = LostReportStatus::cases();

        $lostReports = LostReport::with([
            'reporter:id,nis,name',
            'bookCopy:id,copy_code,book_id',
            'bookCopy.book:id,title',
            'verifier:id,name'
        ])
            ->when($statusFilter && in_array($statusFilter, array_column($validStatuses, 'value')), function ($query) use ($statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->latest('report_date')
            ->get();

        return view('admin.lost-reports.index', compact('lostReports', 'validStatuses', 'statusFilter'));
    }

    public function show(LostReport $lost_report): View
    {
        $lost_report->load([
            'reporter:id,nis,name,class,major,email,phone_number',
            'bookCopy:id,copy_code,status,condition,book_id',
            'bookCopy.book:id,title,isbn,location,cover_image',
            'borrowing' => function ($query) {
                $query->with([
                    'loanProcessor:id,name',
                    'returnProcessor:id,name',
                    'fine'
                ]);
            },
            'verifier:id,name'
        ]);

        $lostBookFee = (int) setting('lost_book_fee', 0);

        return view('admin.lost-reports.show', compact('lost_report', 'lostBookFee'));
    }

    public function verify(VerifyLostReportRequest $request, LostReport $lost_report): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $lost_report->status = LostReportStatus::Verified;
            $lost_report->admin_user_id_verify = Auth::guard('admin')->id();
            $lost_report->save();

            DB::commit();
            return redirect()->route('admin.lost-reports.show', $lost_report)
                ->with('success', 'Laporan kehilangan berhasil diverifikasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error verifying LostReport ID: {$lost_report->id} - " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal verifikasi laporan: Terjadi kesalahan sistem.');
        }
    }

    public function resolve(ResolveLostReportRequest $request, LostReport $lost_report): RedirectResponse
    {
        $validated = $request->validated();
        $resolutionNotes = $validated['resolution_notes'];

        DB::beginTransaction();
        try {
            $lost_report->status = LostReportStatus::Resolved;
            $lost_report->resolution_notes = $resolutionNotes;
            $lost_report->resolution_date = now();
            if (is_null($lost_report->admin_user_id_verify)) {
                $lost_report->admin_user_id_verify = Auth::guard('admin')->id();
            }
            $lost_report->save();

            if ($lost_report->bookCopy) {
                $lost_report->bookCopy->status = BookCopyStatus::Lost;
                $lost_report->bookCopy->save();
            } else {
                Log::warning("BookCopy not found for LostReport ID: {$lost_report->id} during resolution.");
            }

            if ($lost_report->borrowing && in_array($lost_report->borrowing->status, [BorrowingStatus::Borrowed, BorrowingStatus::Overdue])) {
                $lost_report->borrowing->status = BorrowingStatus::Lost;
                $lost_report->borrowing->save();
            }

            $lostBookFee = (int) setting('lost_book_fee', 0);
            if ($lostBookFee > 0 && $lost_report->borrowing_id) {
                $existingFine = Fine::where('borrowing_id', $lost_report->borrowing_id)->first();
                if (!$existingFine) {
                    Fine::create([
                        'borrowing_id' => $lost_report->borrowing_id,
                        'amount' => $lostBookFee,
                        'status' => FineStatus::Unpaid,
                        'notes' => "Denda penggantian buku hilang (Laporan #{$lost_report->id}). " . $resolutionNotes,
                    ]);
                } else {
                    $existingNotes = $existingFine->notes ? trim($existingFine->notes) . "\n\n" : '';
                    $existingFine->notes = $existingNotes . "Buku dinyatakan hilang (Laporan #{$lost_report->id}). Biaya penggantian: Rp " . number_format($lostBookFee, 0, ',', '.') . ". " . $resolutionNotes;
                    $existingFine->save();
                }
            }

            DB::commit();
            event(new LostReportResolved($lost_report));
            return redirect()->route('admin.lost-reports.show', $lost_report)
                ->with('success', 'Laporan kehilangan berhasil diselesaikan (resolved).');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error resolving LostReport ID: {$lost_report->id} - " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyelesaikan laporan: Terjadi kesalahan sistem.');
        }
    }
}
