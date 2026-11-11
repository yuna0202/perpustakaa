<x-layout title="Detail pendaftar" :admin="true">
    <a href="{{ route('admin.pendaftar.index', ['status' => $anggota->status_verifikasi]) }}" class="text-link inline-flex min-h-11 items-center text-sm">Kembali ke pendaftar</a>
    <div class="mt-4 flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-7">
        <div class="min-w-0">
            <h1 class="page-title break-words">{{ $anggota->user->nama }}</h1>
            <p class="mt-3 text-sm tabular-nums text-zinc-500">Terdaftar {{ $anggota->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <x-status :status="$anggota->status_verifikasi"/>
    </div>

    <div class="mt-7 grid gap-5 xl:grid-cols-2">
        <section class="panel min-w-0 p-5 sm:p-6" aria-labelledby="applicant-identity">
            <h2 id="applicant-identity" class="mb-3 text-lg font-semibold tracking-tight text-slate-950">Identitas mahasiswa</h2>
            <dl class="divide-y divide-zinc-100 text-sm">
                @foreach([
                    'Nama lengkap' => $anggota->user->nama,
                    'NIM' => $anggota->nim,
                    'Email' => $anggota->user->email,
                    'Program studi' => $anggota->prodi,
                    'Fakultas' => $anggota->fakultas ?: 'Belum tercatat',
                    'Nomor HP' => $anggota->no_hp,
                ] as $label => $value)
                    <div class="grid gap-1.5 py-3.5 sm:grid-cols-[120px_minmax(0,1fr)] sm:gap-4">
                        <dt class="text-zinc-500">{{ $label }}</dt>
                        <dd class="min-w-0 break-words font-medium text-zinc-950">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <section class="panel min-w-0 p-5 sm:p-6" aria-labelledby="applicant-documents">
            <h2 id="applicant-documents" class="mb-5 text-lg font-semibold tracking-tight text-slate-950">Berkas verifikasi</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">Foto setengah badan</p>
                    <a href="{{ Storage::disk('public')->url($anggota->foto_stnghBadan) }}" target="_blank" rel="noopener" class="block rounded-lg" aria-label="Buka foto setengah badan {{ $anggota->user->nama }} di tab baru">
                        <img src="{{ Storage::disk('public')->url($anggota->foto_stnghBadan) }}" alt="Foto setengah badan {{ $anggota->user->nama }}" width="320" height="240" decoding="async" class="h-52 w-full rounded-lg border border-zinc-200 bg-zinc-50 object-contain">
                    </a>
                </div>
                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">Foto KTM</p>
                    <a href="{{ route('admin.pendaftar.ktm', $anggota) }}" target="_blank" rel="noopener" class="block rounded-lg" aria-label="Buka KTM {{ $anggota->user->nama }} di tab baru">
                        <img src="{{ route('admin.pendaftar.ktm', $anggota) }}" alt="KTM {{ $anggota->user->nama }}" width="320" height="240" decoding="async" class="h-52 w-full rounded-lg border border-zinc-200 bg-zinc-50 object-contain">
                    </a>
                </div>
            </div>
            <p class="mt-4 text-sm leading-6 text-zinc-500">Pilih gambar untuk melihat ukuran penuh. KTM hanya dapat diakses petugas.</p>
        </section>
    </div>

    @if($anggota->status_verifikasi === 'pending')
        <section class="panel mt-5 p-5 sm:p-6" aria-labelledby="review-heading">
            <h2 id="review-heading" class="text-lg font-semibold tracking-tight text-slate-950">Keputusan verifikasi</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-500">Hasil keputusan dikirim ke email mahasiswa.</p>
            @error('status_verifikasi')
                <p class="field-error mt-4" role="alert">{{ $message }}</p>
            @enderror

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <form action="{{ route('admin.pendaftar.review', $anggota) }}" method="POST" class="flex flex-col items-start rounded-xl border border-sky-200 bg-sky-50/50 p-5" data-confirm="Setujui pendaftaran mahasiswa ini dan terbitkan kartu anggota?">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_verifikasi" value="disetujui">
                    <h3 class="font-medium text-zinc-950">Setujui pendaftaran</h3>
                    <p class="mb-5 mt-2 text-sm leading-6 text-zinc-500">Pastikan identitas dan kedua foto sesuai. Persetujuan akan menerbitkan kartu anggota dan QR.</p>
                    <button type="submit" class="btn-primary mt-auto">Setuju</button>
                </form>

                <form action="{{ route('admin.pendaftar.review', $anggota) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-5" data-confirm="Tolak pendaftaran mahasiswa ini dengan alasan yang telah diisi?">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_verifikasi" value="ditolak">
                    <label for="alasan_penolakan" class="field-label">Alasan penolakan</label>
                    <textarea id="alasan_penolakan" name="alasan_penolakan" class="field" rows="3" required maxlength="2000" aria-describedby="alasan_penolakan-description" @if($errors->has('alasan_penolakan')) aria-invalid="true" @endif placeholder="Jelaskan data atau berkas yang perlu diperbaiki">{{ old('alasan_penolakan') }}</textarea>
                    <div id="alasan_penolakan-description">
                        <p class="field-hint">Wajib diisi jika pendaftaran ditolak.</p>
                        @error('alasan_penolakan')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-danger mt-4">Tolak pendaftaran</button>
                </form>
            </div>
        </section>
    @else
        <section class="panel mt-5 p-5 sm:p-6" aria-labelledby="decision-heading">
            <h2 id="decision-heading" class="text-base font-semibold text-zinc-950">Pendaftaran telah diputuskan</h2>
            @if($anggota->status_verifikasi === 'ditolak')
                <p class="mt-4 text-sm font-medium text-zinc-700">Alasan penolakan</p>
                <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-zinc-600">{{ $anggota->alasan_penolakan }}</p>
            @else
                <p class="mt-3 text-sm leading-6 text-zinc-500">Kartu anggota dan QR sudah tersedia pada akun mahasiswa.</p>
            @endif
            <form action="{{ route('admin.pendaftar.kirim-hasil', $anggota) }}" method="POST" class="mt-5" data-confirm="Kirim email hasil verifikasi kepada mahasiswa ini?">
                @csrf
                <button type="submit" class="btn-primary">Kirim email hasil verifikasi</button>
            </form>
        </section>
    @endif
</x-layout>
