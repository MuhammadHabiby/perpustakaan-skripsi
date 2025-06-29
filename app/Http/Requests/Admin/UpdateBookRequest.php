<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        $bookId = $this->route('book')->id;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('books', 'title')->ignore($bookId)
            ],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:authors,id'],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'isbn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')->ignore($bookId)
            ],
            'publication_year' => ['nullable', 'integer', 'digits:4', 'min:1000', 'max:' . date('Y')],
            'synopsis' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'location' => ['nullable', 'string', 'max:100'],
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
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('title')),
        ]);
    }
}
