<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewApplicantRequest;
use App\Http\Requests\ApplicantFilterRequest;
use App\Models\Anggota;
use App\Services\MembershipMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantController extends Controller
{
    public function index(ApplicantFilterRequest $request): View
    {
        $filters = $request->validated();
        $status = $filters['status'] ?? 'pending';
        $q = trim($filters['q'] ?? '');

        $pendaftar = Anggota::with('user')
            ->when($status !== 'semua', fn ($query) => $query->where('status_verifikasi', $status))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(fn ($search) => $search->where('nim', 'like', '%'.$q.'%')
                    ->orWhereHas('user', fn ($user) => $user->where('nama', 'like', '%'.$q.'%')));
            })
            ->latest('id_anggota')->paginate(12)->withQueryString();

        return view('admin.pendaftar.index', compact('pendaftar'));
    }

    public function show(Anggota $anggota): View
    {
        $anggota->load('user');

        return view('admin.pendaftar.show', compact('anggota'));
    }

    public function review(ReviewApplicantRequest $request, Anggota $anggota, MembershipMailer $mailer): RedirectResponse
    {
        $data = $request->validated();

        $anggota = DB::transaction(function () use ($anggota, $data): Anggota {
            $record = Anggota::whereKey($anggota->getKey())->lockForUpdate()->firstOrFail();

            if ($record->status_verifikasi !== 'pending') {
                throw ValidationException::withMessages(['status_verifikasi' => 'Pendaftaran ini sudah diverifikasi. Keputusan tidak dapat dikirim ulang.']);
            }

            $approved = $data['status_verifikasi'] === 'disetujui';
            $record->update([
                'status_verifikasi' => $data['status_verifikasi'],
                'alasan_penolakan' => $approved ? null : $data['alasan_penolakan'],
                'QRcode' => $approved ? Str::random(64) : null,
                'tanggal_terbit' => $approved ? now() : null,
            ]);

            return $record;
        });

        $anggota->load('user');
        $mailSent = $mailer->decision($anggota);
        $response = redirect()->route('admin.pendaftar.show', $anggota)
            ->with('success', 'Pendaftaran berhasil '.$anggota->status_verifikasi.'.');

        if (! $mailSent) {
            $response->with('warning', 'Keputusan sudah tersimpan, tetapi email konfirmasi belum terkirim. Mahasiswa tetap dapat melihat status melalui akunnya.');
        } elseif (in_array(config('mail.default'), ['log', 'array'], true)) {
            $response->with('warning', 'Keputusan tersimpan. Email menggunakan mode pengujian dan belum dikirim ke inbox mahasiswa. Konfigurasikan SMTP untuk pengiriman.');
        }

        return $response;
    }

    public function sendDecision(Anggota $anggota, MembershipMailer $mailer): RedirectResponse
    {
        if (! in_array($anggota->status_verifikasi, ['disetujui', 'ditolak'], true)) {
            return back()->with('warning', 'Keputusan harus ditetapkan sebelum mengirim email.');
        }

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return back()->with('warning', 'Email belum dikirim. Konfigurasikan SMTP karena aplikasi masih menggunakan mode pengujian.');
        }

        $anggota->load('user');
        if (! $mailer->decision($anggota)) {
            return back()->with('warning', 'Email tidak dapat dikirim. Periksa konfigurasi email dan coba lagi.');
        }

        return back()->with('success', 'Pesan hasil verifikasi telah diserahkan ke layanan email untuk '.$anggota->user->email.'.');
    }

    public function ktm(Anggota $anggota): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($anggota->foto_KTM), 404);

        return Storage::disk('local')->response($anggota->foto_KTM, 'ktm.'.pathinfo($anggota->foto_KTM, PATHINFO_EXTENSION), [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
