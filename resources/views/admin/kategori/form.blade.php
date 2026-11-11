<x-layout :title="$kategori->exists ? 'Ubah kategori' : 'Tambah kategori'" :admin="true">
    <a href="{{ route('admin.kategori.index') }}" class="text-link inline-flex min-h-11 items-center text-sm">Kembali ke kategori</a>
    <div class="mt-4 border-b border-slate-200 pb-7">
        <h1 class="page-title">{{ $kategori->exists ? 'Ubah kategori' : 'Tambah kategori' }}</h1>
        <p class="page-intro">Nama kategori harus berbeda dari kategori yang sudah ada.</p>
    </div>

    <form action="{{ $kategori->exists ? route('admin.kategori.update', $kategori) : route('admin.kategori.store') }}" method="POST" class="panel mt-7 max-w-2xl p-5 sm:p-8">
        @csrf
        @if($kategori->exists)
            @method('PUT')
        @endif
        <div class="mb-6 border-b border-slate-200 pb-5">
            <h2 class="text-lg font-semibold tracking-tight text-slate-950">Informasi kategori</h2>
            <p class="mt-1 text-sm text-slate-500">Nama ini akan muncul sebagai filter katalog.</p>
        </div>
        <x-field name="nama_kategori" label="Nama kategori" :value="$kategori->nama_kategori" required maxlength="100" placeholder="Contoh: Teknologi Informasi"/>
        <div class="mt-7 flex flex-wrap gap-3 border-t border-zinc-200 pt-6">
            <button type="submit" class="btn-primary">Simpan kategori</button>
            <a href="{{ route('admin.kategori.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-layout>
