<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateSiteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        $siteUserId = $this->route('site_user')->id;

        return [
            'nis' => [
                'required',
                'string',
                'max:50',
                Rule::unique('site_users', 'nis')->ignore($siteUserId)
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('site_users', 'email')->ignore($siteUserId)
            ],
            'password' => ['nullable', 'string', 'confirmed', Password::min(8)],
            'class' => ['nullable', 'string', 'max:100'],
            'major' => ['nullable', 'string', 'max:100'],
        ];
    }
    public function messages(): array
    {
        return [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter jika diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ];
    }
}
