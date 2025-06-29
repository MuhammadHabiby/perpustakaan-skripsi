<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:books,title'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:authors,id'],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'publication_year' => ['nullable', 'integer', 'digits:4', 'min:1000', 'max:' . date('Y')],
            'synopsis' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'location' => ['nullable', 'string', 'max:100'],
            'initial_copies' => ['required', 'integer', 'min:1'],
            'copy_code_prefix' => ['required', 'string', 'max:90'],
            'copy_code_start' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'title.unique' => 'Judul buku sudah ada.',
            'category_id.exists' => 'Kategori tidak valid.',
            'author_id.exists' => 'Pengarang tidak valid.',
            'publisher_id.exists' => 'Penerbit tidak valid.',
            'isbn.unique' => 'ISBN sudah terdaftar.',
            'isbn.max' => 'ISBN maksimal 20 karakter.',
            'publication_year.digits' => 'Tahun terbit harus 4 digit.',
            'publication_year.max' => 'Tahun terbit tidak boleh melebihi tahun sekarang.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.mimes' => 'Format gambar yang didukung: jpeg, png, jpg, gif, webp.',
            'cover_image.max' => 'Ukuran gambar maksimal 2MB.',
            'location.max' => 'Lokasi maksimal 100 karakter.',
            'initial_copies.required' => 'Jumlah eksemplar awal wajib diisi.',
            'initial_copies.min' => 'Jumlah eksemplar minimal 1.',
            'copy_code_prefix.required' => 'Kode awal eksemplar wajib diisi.',
            'copy_code_prefix.max' => 'Kode awal eksemplar terlalu panjang.',
            'copy_code_start.required' => 'Nomor awal eksemplar wajib diisi.',
            'copy_code_start.min' => 'Nomor awal minimal 1.',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('title')),
        ]);
    }
}
