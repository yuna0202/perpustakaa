<x-layout title="Kartu Anggota Perpustakaan">
    <header class="no-print bg-slate-950 py-8 text-white sm:py-10">
        <div class="container-page">
            <p class="text-xs font-semibold uppercase tracking-wider text-sky-300">UPT Perpustakaan UNIGA</p>
            <h1 class="mt-2 font-serif text-3xl leading-tight tracking-tight sm:text-4xl">Kartu Anggota</h1>
        </div>
    </header>

    <section class="container-page print-wrapper relative -mt-5 pb-12 sm:-mt-6 sm:pb-16">
        <div class="mx-auto max-w-6xl">
            @if($anggota && $anggota->status_verifikasi === 'disetujui')
                <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <x-status status="disetujui"/>
                        <span class="hidden text-xs text-slate-500 sm:inline">Kartu anggota digital UPT Perpustakaan UNIGA</span>
                    </div>
                    <button type="button" class="btn-primary" data-print>
                        <x-icon name="print" class="h-4 w-4"/>
                        <span>Cetak kartu / simpan PDF</span>
                    </button>
                </div>

                <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200 bg-white p-3 text-xs text-slate-600">
                    <span>Ukuran cetak <strong class="text-slate-900">85,6 × 53,98 mm</strong>.</span>
                    <span>QR membuka data verifikasi anggota.</span>
                </div>
                @if($verificationUrl && in_array(strtolower((string) parse_url($verificationUrl, PHP_URL_HOST)), ['localhost', '127.0.0.1', '::1', '[::1]'], true))
                    <div class="no-print mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm leading-6 text-amber-950" role="alert">
                        <strong>QR belum dapat dibuka dari HP karena alamatnya masih localhost.</strong>
                        <span class="block text-xs">Jalankan <code>.\scripts\start-local.ps1</code>, buka halaman kartu melalui alamat LAN yang ditampilkan, lalu muat ulang sebelum mencetak. Ponsel dan komputer harus berada di jaringan yang dapat saling mengakses.</span>
                    </div>
                @endif

                <div class="cards-print-grid grid grid-cols-1 items-start justify-center gap-8 xl:grid-cols-2">
                    <div class="card-side-wrapper flex flex-col items-center">
                        <div class="no-print mb-3 flex w-full max-w-[530px] items-center justify-between px-1 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <span>Sisi depan</span><span class="text-emerald-700">Identitas anggota</span>
                        </div>
                        <article class="membership-card membership-card-front">
                            <header class="member-card-kop">
                                <img src="{{ asset('pengesahan/logo-uniga-official.png') }}" alt="Logo Universitas Gajayana Malang" class="member-card-logo">
                                <div class="member-card-institution">
                                    <strong>UNIVERSITAS GAJAYANA MALANG</strong>
                                    <span>UPT PERPUSTAKAAN</span>
                                </div>
                            </header>
                            <div class="member-card-ribbon">
                                <strong>KARTU TANDA ANGGOTA PERPUSTAKAAN</strong>
                            </div>
                            <div class="member-card-main">
                                <div class="member-card-portrait-column">
                                    <img src="{{ Storage::disk('public')->url($anggota->foto_stnghBadan) }}" alt="Foto {{ $anggota->user->nama }}" class="member-card-portrait">
                                    <span class="member-card-active">ANGGOTA AKTIF</span>
                                </div>
                                <div class="member-card-information">
                                    <h2 class="member-card-name">{{ $anggota->user->nama }}</h2>
                                    <dl class="member-card-facts">
                                        <div><dt>NIM</dt><dd>{{ $anggota->nim }}</dd></div>
                                        <div><dt>Program studi</dt><dd>{{ $anggota->prodi }}</dd></div>
                                        <div><dt>Fakultas</dt><dd>{{ $anggota->fakultas ?: 'Belum tercatat' }}</dd></div>
                                        <div><dt>Tanggal terbit</dt><dd>{{ $anggota->tanggal_terbit?->format('d/m/Y') ?? 'Belum tercatat' }}</dd></div>
                                    </dl>
                                    <div class="member-card-authority">
                                        <div class="member-card-validation-seal" aria-label="Status anggota aktif, validasi digital melalui QR">
                                            <span>VALIDASI</span>
                                            <strong>QR</strong>
                                            <span>DIGITAL</span>
                                        </div>
                                        <div class="member-card-authority-signature">
                                            <span>Tanda tangan pejabat berwenang</span>
                                            <div class="member-card-signature-line" aria-hidden="true"></div>
                                            @if(config('membership.authority_name'))
                                                <strong>{{ config('membership.authority_name') }}</strong>
                                            @endif
                                            <small>{{ config('membership.authority_title', 'Kepala UPT Perpustakaan') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="member-card-bottom-bar" aria-hidden="true"></div>
                        </article>
                    </div>

                    <div class="card-side-wrapper flex flex-col items-center">
                        <div class="no-print mb-3 flex w-full max-w-[530px] items-center justify-between px-1 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <span>Sisi belakang</span><span class="text-sky-800">Ketentuan & QR verifikasi</span>
                        </div>
                        <article class="membership-card membership-card-back">
                            <header class="member-card-back-kop">
                                <strong>VERIFIKASI ANGGOTA</strong>
                                <span>UPT PERPUSTAKAAN UNIGA</span>
                            </header>
                            <div class="member-card-back-body">
                                <ol class="member-card-rules">
                                    <li>Kartu berlaku untuk pemilik yang terdaftar dan disetujui.</li>
                                    <li>Tunjukkan kartu saat menggunakan layanan perpustakaan.</li>
                                </ol>
                                <div class="member-card-qr-panel">
                                    <a href="{{ $verificationUrl }}" class="member-qr" aria-label="Buka verifikasi kartu anggota {{ $anggota->user->nama }}">
                                        {!! $qrCode !!}
                                    </a>
                                    <div class="member-card-qr-copy">
                                        <strong>VERIFIKASI KARTU ANGGOTA</strong>
                                        <span>Scan dengan kamera HP untuk membuka identitas dan status keanggotaan.</span>
                                        <a href="{{ $verificationUrl }}">Buka halaman verifikasi</a>
                                    </div>
                                </div>
                                <div class="member-card-motto">Pindai QR untuk melihat status keanggotaan terbaru.</div>
                            </div>
                            <div class="member-card-bottom-bar" aria-hidden="true"></div>
                        </article>
                    </div>
                </div>

                <div class="no-print mt-8 flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-600 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-slate-900">Petunjuk cetak bolak-balik</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">Cetak pada skala 100% / ukuran sebenarnya, kertas A4, dua sisi dengan balik sisi panjang. Untuk scan HP, buka kartu lewat alamat LAN atau domain yang dapat diakses ponsel.</p>
                    </div>
                    <button type="button" class="btn-secondary shrink-0 text-xs" data-print>Cetak kartu</button>
                </div>
            @elseif($anggota && $anggota->status_verifikasi === 'ditolak')
                <section class="panel rounded-xl p-6 shadow-sm sm:p-8">
                    <x-status status="ditolak"/>
                    <h2 class="mt-5 text-xl font-semibold text-slate-950">Pendaftaran belum disetujui</h2>
                    <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-5">
                        <h3 class="text-sm font-semibold text-red-800">Alasan penolakan</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-red-900">{{ $anggota->alasan_penolakan }}</p>
                    </div>
                    <p class="mt-5 text-sm leading-6 text-slate-600">Hubungi petugas perpustakaan untuk memeriksa data pendaftaran Anda.</p>
                    <a href="{{ route('katalog.index') }}" class="btn-secondary mt-6">Lihat katalog</a>
                </section>
            @else
                <section class="panel rounded-xl p-6 shadow-sm sm:p-8">
                    @if($anggota)<x-status :status="$anggota->status_verifikasi"/>@endif
                    <h2 class="mt-5 text-xl font-semibold text-slate-950">{{ $anggota ? 'Pendaftaran sedang diperiksa' : 'Data anggota belum tersedia' }}</h2>
                    <p class="mt-3 max-w-lg text-sm leading-7 text-slate-600">{{ $anggota ? 'Petugas sedang memeriksa data dan berkas Anda. Hasil verifikasi akan dikirim melalui email.' : 'Hubungi petugas perpustakaan untuk memeriksa data keanggotaan Anda.' }}</p>
                    @if($anggota)
                        <p class="mt-5 border-t border-slate-200 pt-5 text-sm text-slate-500">Kartu dan QR tersedia setelah pendaftaran disetujui.</p>
                    @endif
                    <a href="{{ route('katalog.index') }}" class="btn-secondary mt-6">Lihat katalog</a>
                </section>
            @endif
        </div>
    </section>
</x-layout>
