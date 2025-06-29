<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SiteUser;
use App\Models\BookCopy;
use App\Enum\BookCopyStatus;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'site_user_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $student = SiteUser::find($value);
                    if (!$student) {
                        $fail('Siswa yang dipilih tidak ditemukan.');
                    }
                },
            ],
            'book_copy_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $copy = BookCopy::find($value);
                    if (!$copy) {
                        $fail('Eksemplar buku yang dipilih tidak ditemukan.');
                    } elseif ($copy->status !== BookCopyStatus::Available) {
                        $fail('Eksemplar buku yang dipilih sedang tidak tersedia.');
                    }
                },
            ],
            'borrow_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'site_user_id.required' => 'Siswa peminjam wajib dipilih.',
            'book_copy_id.required' => 'Eksemplar buku wajib dipilih.',
            'borrow_date.date_format' => 'Format tanggal pinjam harus YYYY-MM-DD.',
        ];
    }
}
