<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Anggota;
use App\Models\User;
use App\Services\MembershipMailer;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class RegistrationController extends Controller
{
    public function create(): View
    {
        $left = random_int(1, 9);
        $right = random_int(1, 9);
        session(['registration_captcha' => ['answer' => $left + $right, 'issued_at' => time()]]);

        return view('auth.daftar', ['programStudi' => config('program_studi'), 'captchaLeft' => $left, 'captchaRight' => $right]);
    }

    public function store(RegisterRequest $request, MembershipMailer $mailer): RedirectResponse
    {
        $data = $request->validated();
        $portrait = $ktm = null;

        try {
            $portrait = $request->file('foto_stnghBadan')->store('anggota/foto', 'public');
            $ktm = $request->file('foto_KTM')->store('ktm', 'local');

            if (! $portrait || ! $ktm) {
                throw new RuntimeException('Berkas pendaftaran gagal disimpan.');
            }

            $anggota = DB::transaction(function () use ($data, $portrait, $ktm): Anggota {
                $user = User::create([
                    'nama' => $data['nama'], 'email' => $data['email'],
                    'password' => Hash::make($data['password']), 'role' => 'mahasiswa',
                ]);

                return $user->anggota()->create([
                    'nim' => $data['nim'], 'prodi' => $data['prodi'],
                    'fakultas' => config('program_studi')[$data['prodi']], 'angkatan' => null,
                    'no_hp' => $data['no_hp'], 'foto_stnghBadan' => $portrait, 'foto_KTM' => $ktm,
                    'status_verifikasi' => 'pending', 'QRcode' => null, 'alasan_penolakan' => null,
                ]);
            });
        } catch (Throwable $exception) {
            if ($portrait) {
                Storage::disk('public')->delete($portrait);
            }
            if ($ktm) {
                Storage::disk('local')->delete($ktm);
            }

            if ($exception instanceof UniqueConstraintViolationException) {
                $errors = [];
                if (User::where('email', $data['email'])->exists()) {
                    $errors['email'] = 'Email sudah terdaftar.';
                }
                if (Anggota::where('nim', $data['nim'])->exists()) {
                    $errors['nim'] = 'NIM sudah terdaftar.';
                }
                throw ValidationException::withMessages($errors ?: ['email' => 'Pendaftaran sudah tercatat. Silakan masuk.']);
            }

            throw $exception;
        }

        $anggota->load('user');
        Auth::login($anggota->user);
        $request->session()->regenerate();

        // Send only after the transaction has committed, so mail failure never rolls back membership.
        $mailSent = $mailer->registration($anggota);
        $response = redirect()->route('anggota.kartu')->with('success', 'Pendaftaran berhasil dikirim. Petugas akan memeriksa data Anda.');

        if (! $mailSent) {
            $response->with('warning', 'Data Anda sudah tersimpan, tetapi sebagian email belum terkirim. Pantau status pendaftaran melalui halaman ini.');
        }

        return $response;
    }
}
