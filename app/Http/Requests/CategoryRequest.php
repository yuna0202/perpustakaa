<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return ['nama_kategori' => ['required', 'string', 'max:100', Rule::unique('t_kategori', 'nama_kategori')->ignore($this->route('kategori'))]];
    }
}
