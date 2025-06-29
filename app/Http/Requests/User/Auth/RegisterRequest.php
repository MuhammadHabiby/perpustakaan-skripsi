<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:50', 'unique:site_users,nis'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:site_users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'class' => ['nullable', 'string', 'max:100'],
            'major' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS ini sudah terdaftar. Silakan gunakan NIS lain',
            'name.required' => 'Nama Lengkap wajib diisi.',
            'email.required' => 'Alamat Email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'class.string' => 'Kelas harus berupa teks.',
            'class.max' => 'Kelas maksimal 100 karakter.',
            'major.string' => 'Jurusan harus berupa teks.',
            'major.max' => 'Jurusan maksimal 100 karakter.',
        ];
    }
}
