<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Enum\FineStatus;
use App\Http\Requests\Admin\PayFineRequest;
use App\Http\Requests\Admin\WaiveFineRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FineController extends Controller
{
    public function index(Request $request): View
    {
        $fines = Fine::with([
            'borrowing:id,site_user_id,book_copy_id',
            'borrowing.siteUser:id,nis,name',
            'borrowing.bookCopy:id,copy_code,book_id',
            'borrowing.bookCopy.book:id,title',
            'paymentProcessor:id,name'
        ])
            ->orderByRaw("FIELD(status, '" . FineStatus::Unpaid->value . "', '" . FineStatus::Paid->value . "', '" . FineStatus::Waived->value . "')")
            ->latest('created_at')
            ->get();

        return view('admin.fines.index', compact('fines'));
    }

    public function show(Fine $fine): View
    {
        $fine->load([
            'borrowing' => function ($query) {
                $query->with([
                    'siteUser:id,nis,name,class,major',
                    'bookCopy:id,copy_code,book_id',
                    'bookCopy.book:id,title',
                    'loanProcessor:id,name',
                    'returnProcessor:id,name',
                ]);
            },
            'paymentProcessor:id,name'
        ]);

        return view('admin.fines.show', compact('fine'));
    }

    public function pay(PayFineRequest $request, Fine $fine): RedirectResponse
    {

        $validated = $request->validated();
        $paymentNotes = $validated['payment_notes'] ?? null;

        DB::beginTransaction();
        try {
            $fine->status = FineStatus::Paid;
            $fine->paid_amount = $fine->amount;
            $fine->payment_date = now();
            $fine->admin_user_id_paid = Auth::guard('admin')->id();

            if (!empty($paymentNotes)) {
                $existingNotes = $fine->notes ? trim($fine->notes) . "\n\n" : '';
                $fine->notes = $existingNotes . "Catatan Pembayaran: " . trim($paymentNotes);
            }

            $fine->save();

            DB::commit();

            return redirect()->route('admin.fines.index')
                ->with('success', 'Pembayaran denda berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing fine payment for Fine ID: {$fine->id} - " . $e->getMessage());
            return redirect()->route('admin.fines.index')
                ->with('error', 'Gagal mencatat pembayaran denda: Terjadi Kesalahan Sistem');
        }
    }

    public function waive(WaiveFineRequest $request, Fine $fine): RedirectResponse
    {
        $validated = $request->validated();
        $waiverNotes = $validated['waiver_notes'] ?? null;

        DB::beginTransaction();
        try {
            $fine->status = FineStatus::Waived;
            $fine->paid_amount = 0;
            $fine->payment_date = now();
            $fine->admin_user_id_paid = Auth::guard('admin')->id();

            if (!empty($waiverNotes)) {
                $existingNotes = $fine->notes ? trim($fine->notes) . "\n\n" : '';
                $fine->notes = $existingNotes . "Catatan Pembebasan: " . trim($waiverNotes);
            } else {
                $existingNotes = $fine->notes ? trim($fine->notes) . "\n\n" : '';
                $fine->notes = $existingNotes . "Denda dibebaskan oleh admin.";
            }

            $fine->save();

            DB::commit();

            return redirect()->route('admin.fines.index')
                ->with('success', 'Denda berhasil dibebaskan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing fine waiver for Fine ID: {$fine->id} - " . $e->getMessage());
            return redirect()->route('admin.fines.index')
                ->with('error', 'Gagal membebaskan denda: Terjadi Kesalahan Sistem');
        }
    }
}
