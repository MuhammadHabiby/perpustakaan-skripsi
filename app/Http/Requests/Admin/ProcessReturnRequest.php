<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;
use App\Enum\BorrowingStatus;

class ProcessReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!Auth::guard('admin')->check()) {
            return false;
        }
        $borrowing = $this->route('borrowing');
        return $borrowing instanceof Borrowing &&
            in_array($borrowing->status, [BorrowingStatus::Borrowed, BorrowingStatus::Overdue]);
    }

    public function rules(): array
    {
        return [
            'return_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_notes.string' => 'Catatan pengembalian harus berupa teks.',
            'return_notes.max' => 'Catatan pengembalian terlalu panjang (maksimal 1000 karakter).',
        ];
    }
}
