<x-layout :title="$buku->exists ? 'Ubah buku' : 'Tambah buku'" :admin="true">
    <a href="{{ route('admin.buku.index') }}" class="text-link inline-flex min-h-11 items-center text-sm">Kembali ke koleksi buku</a>
    <div class="mt-4 border-b border-slate-200 pb-7">
        <h1 class="page-title">{{ $buku->exists ? 'Ubah buku' : 'Tambah buku' }}</h1>
        <p class="page-intro">Data yang disimpan langsung tersedia di katalog.</p>
    </div>

    @if($kategori->isEmpty())
        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-900" role="status">
            Belum ada kategori buku.
            <a href="{{ route('admin.kategori.create') }}" class="font-medium underline underline-offset-4">Tambahkan kategori terlebih dahulu</a>.
        </div>
    @endif

    <form action="{{ $buku->exists ? route('admin.buku.update', $buku) : route('admin.buku.store') }}" method="POST" enctype="multipart/form-data" class="panel mt-7 max-w-4xl p-5 sm:p-8">
        @csrf
        @if($buku->exists)
            @method('PUT')
        @endif
        <p class="mb-7 text-sm text-slate-500">Semua kolom wajib diisi kecuali cover buku.</p>
        <section aria-labelledby="book-identity-heading">
            <div class="mb-5">
                <h2 id="book-identity-heading" class="text-lg font-semibold tracking-tight text-slate-950">Informasi buku</h2>
                <p class="mt-1 text-sm text-slate-500">Identitas yang tampil pada katalog publik.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-field name="judul" label="Judul buku" :value="$buku->judul" required maxlength="255"/>
                </div>
                <x-field name="pengarang" label="Pengarang" :value="$buku->pengarang" required maxlength="255"/>
                <div>
                    <label for="id_kategori" class="field-label">Kategori</label>
                    <select id="id_kategori" name="id_kategori" class="field" required aria-describedby="id_kategori-error" @if($errors->has('id_kategori')) aria-invalid="true" @endif>
                        <option value="">Pilih kategori</option>
                        @foreach($kategori as $item)
                            <option value="{{ $item->id_kategori }}" @selected((string) old('id_kategori', $buku->id_kategori) === (string) $item->id_kategori)>{{ $item->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <div id="id_kategori-error">@error('id_kategori')<p class="field-error">{{ $message }}</p>@enderror</div>
                </div>
                <x-field name="penerbit" label="Penerbit" :value="$buku->penerbit" required maxlength="255"/>
                <x-field name="isbn" label="ISBN" :value="$buku->isbn" required maxlength="32" spellcheck="false" hint="ISBN harus berbeda untuk setiap judul."/>
                <x-field name="tahun_terbit" label="Tahun terbit" type="number" :value="$buku->tahun_terbit" required min="1000" :max="date('Y')" placeholder="Contoh: 2026"/>
                <div class="sm:col-span-2">
                    <x-field name="cover" label="Cover buku" type="file" accept="image/jpeg,image/png,image/webp" hint="Opsional. Format JPG, PNG, atau WebP, maksimal 2 MB."/>
                    @if($buku->cover_path)
                        <div class="mt-4 flex flex-wrap items-center gap-4">
                            <img src="{{ Storage::disk('public')->url($buku->cover_path) }}" alt="Cover {{ $buku->judul }}" width="96" height="128" class="h-32 w-24 rounded-lg border border-slate-200 object-contain" />
                            <label class="flex min-h-11 items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="remove_cover" value="1" @checked(old('remove_cover')) class="h-4 w-4"> Hapus cover saat ini</label>
                        </div>
                        <p class="field-hint">Mengunggah cover baru akan menggantikan cover saat ini.</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="mt-9 border-t border-slate-200 pt-8" aria-labelledby="book-inventory-heading">
            <div class="mb-5">
                <h2 id="book-inventory-heading" class="text-lg font-semibold tracking-tight text-slate-950">Inventaris</h2>
                <p class="mt-1 text-sm text-slate-500">Stok dan lokasi yang digunakan untuk mencari buku di perpustakaan.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="jumlah_stok" label="Jumlah stok" type="number" :value="$buku->jumlah_stok ?? 0" required min="0" step="1"/>
                <x-field name="lokasi_rak" label="Lokasi rak" :value="$buku->lokasi_rak" required maxlength="100" placeholder="Contoh: A-01"/>
                <x-field name="tanggal_masuk" label="Tanggal masuk" type="date" :value="$buku->tanggal_masuk?->format('Y-m-d') ?? date('Y-m-d')" required/>
            </div>
        </section>
        <div class="mt-8 flex flex-wrap gap-3 border-t border-zinc-200 pt-6">
            <button type="submit" class="btn-primary" @disabled($kategori->isEmpty())>Simpan buku</button>
            <a href="{{ route('admin.buku.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-layout>
