<x-layout title="Beranda">
    <section class="home-hero" aria-labelledby="home-title">
        <div class="container-page grid gap-12 py-14 sm:py-20 lg:grid-cols-[minmax(0,1.2fr)_minmax(340px,0.8fr)] lg:items-center lg:gap-16 lg:py-28">
            <div class="relative z-10">
                <p class="mb-5 text-xs font-semibold uppercase tracking-[0.16em] text-sky-200">Perpustakaan Universitas Gajayana Malang</p>
                <h1 id="home-title" class="home-title">Cari buku untuk <em>tugas</em> dan riset.</h1>
                <p class="mt-6 max-w-xl text-base leading-8 text-slate-300 sm:text-lg">Temukan judul, periksa stok, dan lihat lokasi rak sebelum datang ke perpustakaan.</p>

                <form action="{{ route('katalog.index') }}" method="GET" role="search" class="home-search-panel mt-9">
                    <div class="min-w-0 flex-1">
                        <label for="home-search" class="mb-2 block text-sm font-medium text-slate-900">Cari dalam katalog</label>
                        <div class="relative">
                            <x-icon name="search" class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-slate-500"/>
                            <input id="home-search" name="q" type="search" class="field !min-h-12 !pl-12" placeholder="Judul, pengarang, atau ISBN" maxlength="100" autocomplete="off">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary !min-h-12 shrink-0 sm:mb-px sm:self-end sm:px-6">Cari buku <x-icon name="arrow" class="h-4 w-4"/></button>
                </form>
                <p class="mt-4 text-sm text-slate-400">Katalog terbuka untuk semua pengunjung.</p>
            </div>

            <aside class="home-insight" aria-label="Ringkasan koleksi">
                <div class="flex items-center gap-3 border-b border-white/15 pb-5">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-200/20 bg-sky-200/10 text-sky-200"><x-icon name="book" class="h-6 w-6"/></span>
                    <div>
                        <p class="text-sm font-semibold text-white">Koleksi perpustakaan</p>
                        <p class="mt-0.5 text-xs text-slate-400">Data katalog saat ini</p>
                    </div>
                </div>
                <dl class="grid grid-cols-2 divide-x divide-white/15 py-7">
                    <div class="pr-5"><dd class="font-display text-5xl leading-none tracking-tight tabular-nums text-sky-200">{{ number_format($totalBuku, 0, ',', '.') }}</dd><dt class="mt-3 text-sm text-slate-300">Judul buku</dt></div>
                    <div class="pl-7"><dd class="font-display text-5xl leading-none tracking-tight tabular-nums text-sky-200">{{ number_format($totalKategori, 0, ',', '.') }}</dd><dt class="mt-3 text-sm text-slate-300">Kategori</dt></div>
                </dl>
                <div class="border-t border-white/15 pt-5">
                    <p class="text-sm leading-6 text-slate-300">Saring koleksi berdasarkan kategori atau lanjutkan ke layanan keanggotaan.</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('katalog.index') }}" class="home-quick-link">Jelajahi katalog <x-icon name="arrow" class="h-4 w-4"/></a>
                        @guest
                            <a href="{{ route('daftar') }}" class="home-quick-link">Daftar anggota <x-icon name="arrow" class="h-4 w-4"/></a>
                        @else
                            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('anggota.kartu') }}" class="home-quick-link">{{ auth()->user()->role === 'admin' ? 'Buka dashboard' : 'Lihat kartu' }} <x-icon name="arrow" class="h-4 w-4"/></a>
                        @endguest
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="container-page py-14 sm:py-20" aria-labelledby="latest-title">
        <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-sky-700">Katalog</p>
                <h2 id="latest-title" class="section-title">Baru di katalog</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Buku yang terakhir ditambahkan petugas.</p>
            </div>
            <a href="{{ route('katalog.index') }}" class="text-link inline-flex min-h-11 items-center gap-2 text-sm">Lihat semua buku <x-icon name="arrow" class="h-4 w-4"/></a>
        </div>
        @if($bukuTerbaru->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($bukuTerbaru as $book)
                    <x-book-card :book="$book"/>
                @endforeach
            </div>
        @else
            <div class="panel empty-state">
                <h3 class="font-medium">Belum ada buku</h3>
                <p class="mt-2 text-sm text-slate-600">Buku yang ditambahkan petugas akan tampil di katalog.</p>
            </div>
        @endif
    </section>

    <section class="border-t border-slate-200 bg-white py-14 sm:py-20" aria-labelledby="member-services">
        <div class="container-page">
            <div class="mb-7 max-w-2xl">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-sky-700">Keanggotaan</p>
                <h2 id="member-services" class="section-title">Layanan anggota</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Pendaftaran dan kartu anggota dapat diakses dari akun mahasiswa.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <article class="service-card">
                    <div class="service-icon"><x-icon name="users" class="h-6 w-6"/></div>
                    <h3 class="mt-6 text-xl font-semibold tracking-tight text-slate-950">Pendaftaran mahasiswa</h3>
                    <p class="mt-3 max-w-md text-sm leading-7 text-slate-600">Lengkapi identitas, foto diri, dan KTM. Petugas akan memeriksa pendaftaran Anda.</p>
                    @guest
                        <a href="{{ route('daftar') }}" class="service-link">Daftar anggota <x-icon name="arrow" class="h-4 w-4"/></a>
                    @else
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.pendaftar.index') : route('anggota.kartu') }}" class="service-link">{{ auth()->user()->role === 'admin' ? 'Tinjau pendaftar' : 'Lihat status pendaftaran' }} <x-icon name="arrow" class="h-4 w-4"/></a>
                    @endguest
                </article>
                <article class="service-card">
                    <div class="service-icon"><x-icon name="card" class="h-6 w-6"/></div>
                    <h3 class="mt-6 text-xl font-semibold tracking-tight text-slate-950">Kartu anggota</h3>
                    <p class="mt-3 max-w-md text-sm leading-7 text-slate-600">Lihat status pendaftaran, lalu cetak kartu setelah keanggotaan disetujui.</p>
                    <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('anggota.kartu') }}" class="service-link">{{ auth()->check() && auth()->user()->role === 'admin' ? 'Buka dashboard' : 'Akses kartu anggota' }} <x-icon name="arrow" class="h-4 w-4"/></a>
                </article>
            </div>
        </div>
    </section>
</x-layout>
