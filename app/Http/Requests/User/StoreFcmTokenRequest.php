<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'fcm_token' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'fcm_token.required' => 'FCM token tidak boleh kosong.',
            'fcm_token.string' => 'FCM token tidak valid.',
            'fcm_token.max' => 'FCM token terlalu panjang.',
        ];
    }
}
