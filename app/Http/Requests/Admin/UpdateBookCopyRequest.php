<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\BookCopyStatus;
use App\Enum\BookCondition;
use Illuminate\Validation\Rules\Enum;

class UpdateBookCopyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(BookCopyStatus::class)],
            'condition' => ['required', new Enum(BookCondition::class)],
        ];
    }
    public function messages(): array
    {
        return [
            'status.required' => 'Status wajib dipilih.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status tidak valid.',
            'condition.required' => 'Kondisi wajib dipilih.',
            'condition.Illuminate\Validation\Rules\Enum' => 'Kondisi tidak valid.',
        ];
    }
}
