<x-layout title="Koleksi buku" :admin="true">
    <div class="flex flex-wrap items-end justify-between gap-4 border-b border-slate-200 pb-7">
        <div>
            <h1 class="page-title">Koleksi buku</h1>
            <p class="page-intro">Kelola data buku, stok, dan lokasi rak.</p>
        </div>
        <a href="{{ route('admin.buku.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4"/>Tambah buku</a>
    </div>

    <form action="{{ route('admin.buku.index') }}" method="GET" role="search" class="panel mt-7 grid items-end gap-4 p-5 sm:p-6 md:grid-cols-[minmax(0,1fr)_220px_auto]">
        <x-field name="q" label="Cari buku" type="search" :value="request('q')" placeholder="Judul, pengarang, atau ISBN" maxlength="100" autocomplete="off"/>
        <div>
            <label for="kategori" class="field-label">Kategori</label>
            <select name="kategori" id="kategori" class="field" aria-describedby="kategori-error" @if($errors->has('kategori')) aria-invalid="true" @endif>
                <option value="">Semua kategori</option>
                @foreach($kategori as $item)
                    <option value="{{ $item->id_kategori }}" @selected((string) request('kategori') === (string) $item->id_kategori)>{{ $item->nama_kategori }}</option>
                @endforeach
            </select>
            <div id="kategori-error">@error('kategori')<p class="field-error">{{ $message }}</p>@enderror</div>
        </div>
        <button type="submit" class="btn-secondary">Cari buku</button>
    </form>

    <div class="my-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm tabular-nums text-zinc-500">{{ number_format($buku->total(), 0, ',', '.') }} judul ditemukan</p>
        @if(request()->filled('q') || request()->filled('kategori'))
            <a href="{{ route('admin.buku.index') }}" class="text-link inline-flex min-h-11 items-center text-sm">Hapus filter</a>
        @endif
    </div>

    <div class="table-wrap border-slate-200 bg-white" role="region" aria-label="Daftar buku" tabindex="0">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col">Buku</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Stok</th>
                    <th scope="col">Lokasi rak</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buku as $item)
                    <tr>
                        <td class="min-w-56 max-w-96">
                            @if($item->cover_path)<img src="{{ Storage::disk('public')->url($item->cover_path) }}" alt="Cover {{ $item->judul }}" width="48" height="64" loading="lazy" class="mb-2 h-16 w-12 rounded border border-slate-200 object-contain" />@endif
                            <a href="{{ route('katalog.show', $item) }}" class="inline-flex min-h-11 items-center break-words font-medium text-zinc-950 underline-offset-4 hover:underline">{{ $item->judul }}</a>
                            <span class="mt-1 block break-words text-xs text-zinc-500">{{ $item->pengarang }}</span>
                            <span class="mt-1 block break-all text-xs tabular-nums text-zinc-500">ISBN {{ $item->isbn }}</span>
                        </td>
                        <td class="min-w-36 max-w-52 break-words">{{ $item->kategori->nama_kategori }}</td>
                        <td class="whitespace-nowrap">
                            <span class="{{ $item->jumlah_stok > 0 ? 'badge-green' : 'badge-red' }}">{{ $item->jumlah_stok > 0 ? 'Tersedia' : 'Habis' }}</span>
                            <span class="mt-1.5 block text-xs tabular-nums text-zinc-500">{{ $item->jumlah_stok }} eksemplar</span>
                        </td>
                        <td class="min-w-28 max-w-44 break-words">{{ $item->lokasi_rak }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.buku.edit', $item) }}" class="text-link inline-flex min-h-11 min-w-11 items-center" aria-label="Ubah buku {{ $item->judul }}">Ubah</a>
                                <form action="{{ route('admin.buku.destroy', $item) }}" method="POST" data-confirm="Hapus buku ini dari koleksi dan katalog publik?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-11 min-w-11 items-center rounded px-2 text-sm font-medium text-red-700 underline-offset-4 hover:underline" aria-label="Hapus buku {{ $item->judul }}">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <h2 class="font-medium text-zinc-950">Tidak ada buku yang sesuai</h2>
                                <p class="mt-2 text-sm text-zinc-500">Ubah filter atau tambahkan buku.</p>
                                <a href="{{ route('admin.buku.create') }}" class="btn-primary mt-5">Tambah buku</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $buku->links() }}</div>
</x-layout>
