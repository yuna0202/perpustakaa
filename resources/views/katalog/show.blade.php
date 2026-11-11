<x-layout :title="$buku->judul">
    <header class="bg-slate-950 pb-16 pt-8 text-white sm:pb-20 sm:pt-12">
        <div class="container-page">
            <a href="{{ route('katalog.index', request()->only(['q', 'kategori', 'page'])) }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-slate-300 hover:text-white">
                <x-icon name="arrow" class="h-4 w-4 rotate-180"/>
                Kembali ke katalog
            </a>
            <p class="mt-8 text-sm font-medium text-sky-300">{{ $buku->kategori->nama_kategori }}</p>
            <h1 class="mt-3 max-w-4xl break-words font-serif text-4xl leading-tight tracking-tight sm:text-5xl">{{ $buku->judul }}</h1>
            <p class="mt-4 text-base text-slate-300">Oleh {{ $buku->pengarang }}</p>
        </div>
    </header>

    <section class="container-page relative -mt-7 pb-12 sm:-mt-9 sm:pb-16">
        <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_320px] lg:gap-6">
            <section class="panel p-6 sm:p-8" aria-labelledby="book-information">
                <div class="mb-6 flex h-64 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
                    @if($buku->cover_path)
                        <img src="{{ Storage::disk('public')->url($buku->cover_path) }}" alt="Cover {{ $buku->judul }}" width="220" height="256" class="h-full w-full object-contain" />
                    @else
                        <div class="flex flex-col items-center gap-2 text-slate-500"><x-icon name="book" class="h-12 w-12"/><span class="text-sm">Cover belum tersedia</span></div>
                    @endif
                </div>
                <h2 id="book-information" class="text-lg font-semibold text-slate-950">Informasi buku</h2>
                <p class="mt-1 text-sm text-slate-500">Detail bibliografi dan pencatatan koleksi.</p>
                <dl class="mt-5 divide-y divide-slate-100 text-sm">
                        @foreach(['Penerbit' => $buku->penerbit, 'ISBN' => $buku->isbn, 'Tahun terbit' => $buku->tahun_terbit, 'Kategori' => $buku->kategori->nama_kategori, 'Tanggal masuk' => $buku->tanggal_masuk->format('d/m/Y')] as $label => $value)
                            <div class="grid gap-1 py-4 sm:grid-cols-[140px_minmax(0,1fr)] sm:gap-6">
                                <dt class="text-slate-500">{{ $label }}</dt>
                                <dd class="break-words font-medium text-slate-950">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
            </section>

            <section class="panel p-6 sm:p-7" aria-labelledby="book-availability">
                <h2 id="book-availability" class="text-lg font-semibold text-slate-950">Ketersediaan</h2>
                <div class="mt-4">
                    @if($buku->jumlah_stok > 0)
                        <span class="badge-green">Tersedia</span>
                    @else
                        <span class="badge-red">Habis</span>
                    @endif
                </div>
                <p class="mt-3 text-4xl font-semibold tabular-nums tracking-tight text-slate-950">{{ $buku->jumlah_stok }} <span class="text-sm font-normal tracking-normal text-slate-500">eksemplar</span></p>
                <dl class="mt-6 border-t border-slate-200 pt-5 text-sm">
                    <dt class="text-slate-500">Lokasi rak</dt>
                    <dd class="mt-2 break-words text-base font-semibold text-slate-950">{{ $buku->lokasi_rak }}</dd>
                </dl>
            </section>
        </div>
    </section>
</x-layout>
