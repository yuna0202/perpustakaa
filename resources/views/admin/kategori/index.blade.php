<x-layout title="Kategori buku" :admin="true">
    <div class="flex flex-wrap items-end justify-between gap-4 border-b border-slate-200 pb-7">
        <div>
            <h1 class="page-title">Kategori buku</h1>
            <p class="page-intro">Kategori untuk pengelompokan koleksi.</p>
        </div>
        <a href="{{ route('admin.kategori.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4"/>Tambah kategori</a>
    </div>

    <div class="table-wrap mt-7 border-slate-200 bg-white" role="region" aria-label="Daftar kategori buku" tabindex="0">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col">Nama kategori</th>
                    <th scope="col">Jumlah judul</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategori as $item)
                    <tr>
                        <td class="min-w-52 max-w-md break-words font-medium text-zinc-950">{{ $item->nama_kategori }}</td>
                        <td class="tabular-nums text-zinc-600">{{ $item->buku_count }} buku</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.kategori.edit', $item) }}" class="text-link inline-flex min-h-11 min-w-11 items-center" aria-label="Ubah kategori {{ $item->nama_kategori }}">Ubah</a>
                                <form action="{{ route('admin.kategori.destroy', $item) }}" method="POST" data-confirm="Hapus kategori ini? Kategori yang masih digunakan buku tidak dapat dihapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-11 min-w-11 items-center rounded px-2 text-sm font-medium text-red-700 underline-offset-4 hover:underline" aria-label="Hapus kategori {{ $item->nama_kategori }}">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state">
                                <h2 class="font-medium text-zinc-950">Belum ada kategori</h2>
                                <p class="mt-2 text-sm text-zinc-500">Tambahkan kategori sebelum memasukkan buku.</p>
                                <a href="{{ route('admin.kategori.create') }}" class="btn-primary mt-5">Tambah kategori</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-4 text-sm leading-6 text-zinc-500">Kategori yang masih digunakan oleh buku tidak dapat dihapus.</p>
    <div class="mt-6">{{ $kategori->links() }}</div>
</x-layout>
