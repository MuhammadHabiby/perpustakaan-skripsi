<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Fine;
use App\Enum\FineStatus;

class PayFineRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!Auth::guard('admin')->check()) {
            return false;
        }
        $fine = $this->route('fine');
        return $fine instanceof Fine && $fine->status === FineStatus::Unpaid;
    }

    public function rules(): array
    {
        return [
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_notes.string' => 'Catatan pembayaran harus berupa teks.',
            'payment_notes.max' => 'Catatan pembayaran terlalu panjang (maksimal 1000 karakter).',
        ];
    }
}
