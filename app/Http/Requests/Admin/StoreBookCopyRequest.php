<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\BookCondition;
use Illuminate\Validation\Rules\Enum;

class StoreBookCopyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'copy_code' => ['required', 'string', 'max:100', 'unique:book_copies,copy_code'],
            'condition' => ['required', new Enum(BookCondition::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'copy_code.required' => 'Kode Eksemplar wajib diisi.',
            'copy_code.unique' => 'Kode Eksemplar sudah ada.',
            'copy_code.max' => 'Kode Eksemplar maksimal 100 karakter.',
            'condition.required' => 'Kondisi wajib dipilih.',
            'condition.Illuminate\Validation\Rules\Enum' => 'Kondisi tidak valid.',
        ];
    }
}
