<x-layout title="Verifikasi Kartu Anggota">
    <header class="bg-slate-950 py-8 text-white sm:py-10">
        <div class="container-page">
            <div class="flex items-center gap-3">
                <img src="{{ asset('pengesahan/logo-uniga-official.png') }}" alt="Logo Universitas Gajayana Malang" class="h-10 w-10 rounded-lg bg-white p-1 object-contain">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-300">Universitas Gajayana Malang</p>
                    <p class="text-sm font-medium text-sky-300">UPT Perpustakaan</p>
                </div>
            </div>
            <h1 class="mt-4 font-serif text-3xl leading-tight tracking-tight sm:text-4xl">Verifikasi Anggota</h1>
        </div>
    </header>

    <section class="container-page relative -mt-5 pb-12 sm:-mt-6 sm:pb-16">
        <div class="mx-auto max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
            <div class="flex items-start gap-3 border-b border-emerald-200 bg-emerald-50 p-5 sm:p-6">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-emerald-950">Kartu anggota terverifikasi</h2>
                    <p class="mt-1 text-sm leading-6 text-emerald-900">Status pendaftaran anggota ini disetujui oleh UPT Perpustakaan Universitas Gajayana Malang.</p>
                </div>
            </div>

            <article class="p-5 sm:p-8">
                <div class="flex flex-col items-center gap-5 border-b border-slate-200 pb-6 text-center sm:flex-row sm:items-start sm:text-left">
                    @if($anggota->foto_stnghBadan)
                        <img src="{{ Storage::disk('public')->url($anggota->foto_stnghBadan) }}" alt="Foto {{ $anggota->user->nama }}" width="112" height="144" class="h-36 w-28 shrink-0 rounded-xl border-4 border-amber-200 bg-slate-100 object-cover shadow-sm">
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold tracking-wide text-emerald-900">
                            <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                            ANGGOTA AKTIF
                        </div>
                        <h3 class="mt-3 break-words text-2xl font-bold uppercase tracking-tight text-slate-950">{{ $anggota->user->nama }}</h3>
                        <p class="mt-1 font-mono text-base font-semibold text-slate-600">NIM {{ $anggota->nim }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Kartu Tanda Anggota UPT Perpustakaan Universitas Gajayana Malang</p>
                    </div>
                </div>

                <dl class="divide-y divide-slate-100">
                    <div class="grid grid-cols-[minmax(110px,0.7fr)_minmax(0,1.3fr)] gap-4 py-3.5 text-sm sm:grid-cols-[160px_minmax(0,1fr)]">
                        <dt class="text-slate-500">NIM</dt><dd class="break-all font-mono font-bold text-slate-950">{{ $anggota->nim }}</dd>
                    </div>
                    <div class="grid grid-cols-[minmax(110px,0.7fr)_minmax(0,1.3fr)] gap-4 py-3.5 text-sm sm:grid-cols-[160px_minmax(0,1fr)]">
                        <dt class="text-slate-500">Program studi</dt><dd class="break-words text-slate-900">{{ $anggota->prodi }}</dd>
                    </div>
                    <div class="grid grid-cols-[minmax(110px,0.7fr)_minmax(0,1.3fr)] gap-4 py-3.5 text-sm sm:grid-cols-[160px_minmax(0,1fr)]">
                        <dt class="text-slate-500">Fakultas</dt><dd class="break-words text-slate-900">{{ $anggota->fakultas ?: 'Belum tercatat' }}</dd>
                    </div>
                    <div class="grid grid-cols-[minmax(110px,0.7fr)_minmax(0,1.3fr)] gap-4 py-3.5 text-sm sm:grid-cols-[160px_minmax(0,1fr)]">
                        <dt class="text-slate-500">Tanggal terbit</dt><dd class="tabular-nums font-medium text-slate-900">{{ $anggota->tanggal_terbit?->format('d/m/Y') ?? 'Belum tercatat' }}</dd>
                    </div>
                    <div class="grid grid-cols-[minmax(110px,0.7fr)_minmax(0,1.3fr)] gap-4 py-3.5 text-sm sm:grid-cols-[160px_minmax(0,1fr)]">
                        <dt class="text-slate-500">Status anggota</dt><dd><span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-900">Disetujui</span></dd>
                    </div>
                    <div class="grid grid-cols-[minmax(110px,0.7fr)_minmax(0,1.3fr)] gap-4 py-3.5 text-sm sm:grid-cols-[160px_minmax(0,1fr)]">
                        <dt class="text-slate-500">Instansi penerbit</dt><dd class="font-medium text-slate-900">UPT Perpustakaan Universitas Gajayana Malang</dd>
                    </div>
                </dl>

                <p class="mt-5 border-t border-slate-200 pt-4 text-xs leading-5 text-slate-500">Status ini mengikuti data keanggotaan pada sistem UPT Perpustakaan.</p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('katalog.index') }}" class="btn-primary flex-1 justify-center">Buka katalog buku</a>
                    <a href="{{ route('home') }}" class="btn-secondary justify-center">Beranda</a>
                </div>
            </article>
        </div>
    </section>
</x-layout>
