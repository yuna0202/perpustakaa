<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:255'],
            'id_kategori' => ['required', 'integer', 'exists:t_kategori,id_kategori'],
            'penerbit' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:32', Rule::unique('t_buku', 'isbn')->ignore($this->route('buku'))],
            'tahun_terbit' => ['required', 'digits:4', 'integer', 'min:1000', 'max:'.now()->year],
            'jumlah_stok' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'lokasi_rak' => ['required', 'string', 'max:100'],
            'tanggal_masuk' => ['required', 'date_format:Y-m-d'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['sometimes', 'boolean'],
        ];
    }
}
