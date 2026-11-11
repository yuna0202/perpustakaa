<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Closure;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->email)) {
            $this->merge(['email' => mb_strtolower(trim($this->email))]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'nim' => ['required', 'string', 'max:30', 'unique:t_anggota,nim'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:t_user,email'],
            'password' => ['required', 'string', 'max:255', Password::min(8)->mixedCase()->numbers()->symbols()],
            'prodi' => ['required', 'string', Rule::in(array_keys(config('program_studi')))],
            'captcha' => ['required', 'integer', function (string $attribute, mixed $value, Closure $fail): void {
                $challenge = $this->session()->pull('registration_captcha');
                if (! is_array($challenge) || ! isset($challenge['answer'], $challenge['issued_at']) ||
                    time() - $challenge['issued_at'] > 600 || (int) $value !== $challenge['answer']) {
                    $fail('Jawaban verifikasi salah atau kedaluwarsa. Muat ulang halaman dan coba lagi.');
                }
            }],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[+0-9][0-9\s()\-]{7,19}$/'],
            'foto_stnghBadan' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:2048'],
            'foto_KTM' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama lengkap', 'nim' => 'NIM', 'prodi' => 'program studi',
            'no_hp' => 'nomor HP', 'foto_stnghBadan' => 'foto setengah badan', 'foto_KTM' => 'foto KTM',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah terdaftar. Silakan masuk dengan akun Anda.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'no_hp.regex' => 'Masukkan nomor HP yang valid, misalnya 081234567890.',
            'captcha.required' => 'Isi jawaban verifikasi.',
            'captcha.integer' => 'Jawaban verifikasi harus berupa angka.',
            'foto_stnghBadan.max' => 'Ukuran foto setengah badan maksimal 2 MB.',
            'foto_KTM.max' => 'Ukuran foto KTM maksimal 2 MB.',
        ];
    }
}
