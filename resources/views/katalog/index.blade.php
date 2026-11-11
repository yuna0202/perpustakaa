<x-layout title="Katalog buku">
    <header class="bg-slate-950 pb-24 pt-12 text-white sm:pb-28 sm:pt-16">
        <div class="container-page flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-2xl">
                <p class="text-sm font-medium text-sky-300">Koleksi perpustakaan</p>
                <h1 class="mt-4 font-serif text-4xl leading-tight tracking-tight sm:text-5xl">Temukan buku yang Anda cari.</h1>
                <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300">Cari judul, pengarang, atau ISBN, lalu periksa stok dan lokasi rak sebelum berkunjung.</p>
            </div>
            <p class="border-l border-slate-700 pl-5 text-sm tabular-nums text-slate-300">
                <strong class="block text-3xl font-semibold text-white">{{ number_format($buku->total(), 0, ',', '.') }}</strong>
                judul ditemukan
            </p>
        </div>
    </header>

    <section class="container-page relative -mt-12 pb-12 sm:-mt-14 sm:pb-16">
        <form action="{{ route('katalog.index') }}" method="GET" class="grid items-end gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 md:grid-cols-[minmax(0,1fr)_220px_auto]" role="search">
            <div>
                <label for="q" class="field-label">Cari buku</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 top-3 h-5 w-5 text-slate-400"/>
                    <input type="search" name="q" id="q" value="{{ request('q') }}" class="field !pl-11" placeholder="Judul, pengarang, atau ISBN" maxlength="100" autocomplete="off" @if($errors->has('q')) aria-invalid="true" aria-describedby="q-error" @endif>
                </div>
                @error('q')<p id="q-error" class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="kategori" class="field-label">Kategori</label>
                <select name="kategori" id="kategori" class="field" @if($errors->has('kategori')) aria-invalid="true" aria-describedby="kategori-error" @endif>
                    <option value="">Semua kategori</option>
                    @foreach($kategori as $item)
                        <option value="{{ $item->id_kategori }}" @selected((string) request('kategori') === (string) $item->id_kategori)>{{ $item->nama_kategori }}</option>
                    @endforeach
                </select>
                @error('kategori')<p id="kategori-error" class="field-error">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary w-full md:w-auto">Cari buku</button>
        </form>

        @if(request()->filled('q') || request()->filled('kategori'))
            <div class="mt-5 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600">
                <p>
                    Filter aktif:
                    @if(request()->filled('q'))<strong class="font-medium text-slate-950">{{ request('q') }}</strong>@endif
                    @if(request()->filled('q') && request()->filled('kategori'))<span class="mx-1 text-slate-400">dan</span>@endif
                    @if(request()->filled('kategori'))<strong class="font-medium text-slate-950">{{ $kategori->firstWhere('id_kategori', request('kategori'))?->nama_kategori ?? 'Kategori terpilih' }}</strong>@endif
                </p>
                <a href="{{ route('katalog.index') }}" class="text-link inline-flex min-h-11 items-center">Hapus filter</a>
            </div>
        @endif

        @if($buku->isNotEmpty())
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 sm:gap-5">
                @foreach($buku as $book)
                    <x-book-card :book="$book"/>
                @endforeach
            </div>
            <div class="mt-8">{{ $buku->links() }}</div>
        @else
            <div class="panel empty-state mt-8 border-dashed bg-white">
                <h2 class="text-xl font-semibold text-slate-950">Belum ada buku yang sesuai</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Coba kata kunci lain atau hapus filter kategori.</p>
                <a href="{{ route('katalog.index') }}" class="btn-secondary mt-6">Tampilkan semua buku</a>
            </div>
        @endif
    </section>
</x-layout>
