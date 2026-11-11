@props(['book'])
<article class="book-record group">
    <div class="mb-5 flex h-48 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
        @if($book->cover_path)
            <img src="{{ Storage::disk('public')->url($book->cover_path) }}" alt="Cover {{ $book->judul }}" width="180" height="192" loading="lazy" class="h-full w-full object-contain" />
        @else
            <div class="flex flex-col items-center gap-2 text-slate-500"><x-icon name="book" class="h-10 w-10"/><span class="text-xs">Cover belum tersedia</span></div>
        @endif
    </div>
    <div class="mb-7 flex items-center justify-between gap-3">
        <span class="min-w-0 truncate text-xs font-semibold text-sky-700">{{ $book->kategori->nama_kategori }}</span>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700"><x-icon name="book" class="h-[18px] w-[18px]"/></span>
    </div>
    <h3 class="font-display text-[1.45rem] font-normal leading-7 tracking-tight text-slate-950">
        <a href="{{ route('katalog.show', array_merge(['buku' => $book], request()->only(['q', 'kategori', 'page']))) }}" class="book-title-link" aria-label="Lihat detail {{ $book->judul }}">{{ $book->judul }}</a>
    </h3>
    <p class="mt-3 break-words text-sm leading-6 text-slate-600">{{ $book->pengarang }}</p>
    <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs text-slate-600">
        <div class="flex items-center gap-2.5">
            @if($book->jumlah_stok > 0)<span class="badge-green">Tersedia</span>@else<span class="badge-red">Habis</span>@endif
            <span class="tabular-nums">{{ $book->jumlah_stok }} eksemplar</span>
        </div>
        <span>{{ $book->lokasi_rak }}</span>
    </div>
</article>
