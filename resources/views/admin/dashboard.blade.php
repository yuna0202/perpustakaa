<x-layout title="Dashboard" :admin="true">
    <section class="rounded-2xl bg-slate-950 px-6 py-8 text-white sm:px-9 sm:py-10" aria-labelledby="dashboard-title">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-sky-200">Ruang pengelolaan</p>
                <h1 id="dashboard-title" class="mt-3 font-display text-4xl font-semibold tracking-tight sm:text-5xl">Dashboard</h1>
                <p class="mt-3 max-w-xl text-sm leading-7 text-slate-300">Pantau pendaftaran anggota dan ketersediaan koleksi dari satu tempat.</p>
            </div>
            <a href="{{ route('admin.buku.create') }}" class="btn inline-flex bg-sky-200 text-slate-950 hover:bg-sky-100"><x-icon name="plus" class="h-4 w-4"/>Tambah buku</a>
        </div>
    </section>

    <dl class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Pendaftar menunggu', $pending, 'Belum diverifikasi'],
            ['Anggota disetujui', $totalAnggota, 'Anggota terverifikasi'],
            ['Total judul buku', $totalBuku, 'Tercatat di katalog'],
            ['Buku stok habis', $stokHabis, 'Jumlah stok nol'],
        ] as [$label, $count, $description])
            <div class="panel flex min-h-40 flex-col border-slate-200 p-5 sm:p-6">
                <dt class="text-sm font-medium text-slate-600">{{ $label }}</dt>
                <dd class="mt-3 font-display text-4xl font-semibold tracking-tight tabular-nums text-slate-950">{{ number_format($count, 0, ',', '.') }}</dd>
                <dd class="mt-auto pt-3 text-xs text-slate-500">{{ $description }}</dd>
            </div>
        @endforeach
    </dl>

    <section class="mt-11" aria-labelledby="recent-applicants">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-sky-700">Keanggotaan</p>
                <h2 id="recent-applicants" class="mt-1 text-xl font-semibold tracking-tight text-slate-950">Pendaftaran menunggu verifikasi</h2>
            </div>
            <a href="{{ route('admin.pendaftar.index') }}" class="text-link inline-flex min-h-11 items-center text-sm">Lihat semua pendaftar</a>
        </div>
        <div class="table-wrap" role="region" aria-label="Daftar pendaftaran terbaru" tabindex="0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Mahasiswa</th>
                        <th scope="col">Program studi</th>
                        <th scope="col">Tanggal daftar</th>
                        <th scope="col">Status</th>
                        <th scope="col">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftarTerbaru as $item)
                        <tr>
                            <td class="min-w-48 max-w-72">
                                <span class="block break-words font-medium text-zinc-950">{{ $item->user->nama }}</span>
                                <span class="mt-1 block break-all text-xs text-zinc-500">{{ $item->nim }}</span>
                            </td>
                            <td class="min-w-40 max-w-64 break-words">{{ $item->prodi }}</td>
                            <td class="whitespace-nowrap tabular-nums text-zinc-600">{{ $item->created_at->format('d/m/Y') }}</td>
                            <td><x-status :status="$item->status_verifikasi"/></td>
                            <td>
                                <a href="{{ route('admin.pendaftar.show', $item) }}" class="text-link inline-flex min-h-11 items-center whitespace-nowrap" aria-label="Tinjau pendaftaran {{ $item->user->nama }}">Tinjau</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <p class="font-medium text-zinc-950">Tidak ada pendaftaran menunggu</p>
                                    <p class="mt-2 text-sm text-zinc-500">Pendaftaran baru akan ditampilkan di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-sm">
        <a href="{{ route('admin.buku.index') }}" class="text-link inline-flex min-h-11 items-center">Kelola buku</a>
        <a href="{{ route('admin.kategori.index') }}" class="text-link inline-flex min-h-11 items-center">Kelola kategori</a>
    </div>
</x-layout>
