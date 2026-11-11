<x-layout title="Pendaftar anggota" :admin="true">
    <div class="border-b border-slate-200 pb-7">
        <h1 class="page-title">Pendaftar anggota</h1>
        <p class="page-intro">Periksa identitas dan berkas sebelum memberi keputusan.</p>
    </div>

    <form action="{{ route('admin.pendaftar.index') }}" method="GET" role="search" class="panel mt-7 grid items-end gap-4 p-5 sm:p-6 md:grid-cols-[minmax(0,1fr)_220px_auto]">
        <x-field name="q" label="Cari pendaftar" type="search" :value="request('q')" placeholder="Nama atau NIM" maxlength="100" autocomplete="off"/>
        <div>
            <label for="status" class="field-label">Status verifikasi</label>
            <select name="status" id="status" class="field" aria-describedby="status-error" @if($errors->has('status')) aria-invalid="true" @endif>
                @foreach(['pending' => 'Menunggu verifikasi', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'semua' => 'Semua status'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status', 'pending') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div id="status-error">@error('status')<p class="field-error">{{ $message }}</p>@enderror</div>
        </div>
        <button type="submit" class="btn-secondary">Terapkan</button>
    </form>

    <div class="my-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm tabular-nums text-zinc-500">{{ number_format($pendaftar->total(), 0, ',', '.') }} pendaftar ditemukan</p>
        @if(request()->filled('q'))
            <a href="{{ route('admin.pendaftar.index', ['status' => request('status', 'pending')]) }}" class="text-link inline-flex min-h-11 items-center text-sm">Hapus pencarian</a>
        @endif
    </div>

    <div class="table-wrap border-slate-200 bg-white" role="region" aria-label="Daftar pendaftar anggota" tabindex="0">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col">Mahasiswa</th>
                    <th scope="col">Program studi</th>
                    <th scope="col">Fakultas</th>
                    <th scope="col">Tanggal daftar</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftar as $item)
                    <tr>
                        <td class="min-w-48 max-w-72">
                            <span class="block break-words font-medium text-zinc-950">{{ $item->user->nama }}</span>
                            <span class="mt-1 block break-all text-xs text-zinc-500">{{ $item->nim }}</span>
                        </td>
                        <td class="min-w-40 max-w-64 break-words">{{ $item->prodi }}</td>
                        <td class="min-w-48 max-w-64 break-words">{{ $item->fakultas ?: 'Belum tercatat' }}</td>
                        <td class="whitespace-nowrap tabular-nums text-zinc-600">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td><x-status :status="$item->status_verifikasi"/></td>
                        <td>
                            <a href="{{ route('admin.pendaftar.show', $item) }}" class="text-link inline-flex min-h-11 items-center whitespace-nowrap" aria-label="Lihat detail pendaftar {{ $item->user->nama }}">Lihat detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <h2 class="font-medium text-zinc-950">Tidak ada pendaftar yang sesuai</h2>
                                <p class="mt-2 text-sm text-zinc-500">Ubah status atau kata kunci pencarian.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $pendaftar->links() }}</div>
</x-layout>
