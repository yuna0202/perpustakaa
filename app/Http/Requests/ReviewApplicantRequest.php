<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'status_verifikasi' => ['required', Rule::in(['disetujui', 'ditolak'])],
            'alasan_penolakan' => ['required_if:status_verifikasi,ditolak', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return ['status_verifikasi' => 'hasil verifikasi', 'alasan_penolakan' => 'alasan penolakan'];
    }

    public function messages(): array
    {
        return ['alasan_penolakan.required_if' => 'Alasan penolakan wajib diisi ketika pendaftaran ditolak.'];
    }
}
