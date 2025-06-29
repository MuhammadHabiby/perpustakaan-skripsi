<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;
use App\Models\LostReport;
use App\Enum\BorrowingStatus;

class ReportLostBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $borrowing = $this->route('borrowing');
        $user = Auth::user();

        if (!$user || !$borrowing instanceof Borrowing) {
            return false;
        }
        if ($borrowing->site_user_id !== $user->id) {
            return false;
        }
        if (!in_array($borrowing->status, [BorrowingStatus::Borrowed, BorrowingStatus::Overdue])) {
            return false;
        }
        if (LostReport::where('borrowing_id', $borrowing->id)->exists()) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
