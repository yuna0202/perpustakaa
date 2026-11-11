<x-layout title="Pendaftaran anggota">
    @php($selectedProgram = old('prodi'))
    <header class="bg-slate-950 py-12 text-white sm:py-16">
        <div class="container-page">
            <p class="text-sm font-medium text-sky-300">Keanggotaan perpustakaan</p>
            <h1 class="mt-4 max-w-3xl font-serif text-4xl leading-tight tracking-tight sm:text-5xl">Daftar menjadi anggota.</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">Isi data mahasiswa dan unggah foto untuk diperiksa petugas. Status pendaftaran dapat dilihat setelah masuk.</p>
        </div>
    </header>

    <section class="container-page relative -mt-5 pb-12 sm:-mt-7 sm:pb-16">
        <div class="mx-auto max-w-4xl">
            <form action="{{ route('daftar.store') }}" method="POST" enctype="multipart/form-data" class="panel rounded-xl p-6 shadow-sm sm:p-9" data-confirm="Apakah data sudah terisi dengan benar?">
                @csrf
                <div class="mb-8 border-b border-slate-200 pb-6">
                    <h2 class="text-xl font-semibold tracking-tight text-slate-950">Formulir pendaftaran</h2>
                    <p class="mt-2 text-sm text-slate-500">Semua kolom wajib diisi. Pastikan berkas sesuai sebelum dikirim.</p>
                </div>

                <fieldset>
                    <legend class="mb-5 text-base font-semibold text-slate-950">Identitas mahasiswa</legend>
                    <div class="grid gap-5 sm:grid-cols-2 sm:gap-6">
                        <x-field name="nama" label="Nama lengkap" required autocomplete="name" maxlength="150" placeholder="Sesuai identitas mahasiswa"/>
                        <x-field name="nim" label="NIM" required maxlength="30" spellcheck="false" placeholder="Nomor induk mahasiswa"/>
                        <div>
                            <label for="prodi" class="field-label">Program studi</label>
                            <select id="prodi" name="prodi" class="field" required data-faculty-select @if($errors->has('prodi')) aria-invalid="true" aria-describedby="prodi-error" @endif>
                                <option value="">Pilih program studi</option>
                                @foreach($programStudi as $prodi => $fakultas)
                                    <option value="{{ $prodi }}" data-faculty="{{ $fakultas }}" @selected(old('prodi') === $prodi)>{{ $prodi }}</option>
                                @endforeach
                            </select>
                            @error('prodi')<p id="prodi-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="fakultas" class="field-label">Fakultas</label>
                            <input id="fakultas" type="text" class="field bg-slate-50" value="{{ is_string($selectedProgram) ? ($programStudi[$selectedProgram] ?? '') : '' }}" placeholder="Terisi setelah memilih program studi" readonly data-faculty-output aria-describedby="fakultas-hint">
                            <p id="fakultas-hint" class="field-hint">Fakultas ditentukan dari program studi.</p>
                            <noscript><p class="field-hint">Fakultas ditentukan otomatis saat pendaftaran diproses.</p></noscript>
                        </div>
                        <x-field name="no_hp" label="Nomor HP" type="tel" required autocomplete="tel" maxlength="20" placeholder="081234567890"/>
                    </div>
                </fieldset>

                <fieldset class="mt-9 border-t border-slate-200 pt-8">
                    <legend class="mb-5 text-base font-semibold text-slate-950">Akun perpustakaan</legend>
                    <div class="grid gap-5 sm:grid-cols-2 sm:gap-6">
                        <x-field name="email" label="Email" type="email" required autocomplete="email" maxlength="255" spellcheck="false" hint="Hasil verifikasi dikirim ke alamat ini."/>
                        <div>
                            <label for="password" class="field-label">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" class="field pr-14 @error('password') border-red-500 @enderror" required autocomplete="new-password" minlength="8" aria-describedby="password-description" @error('password') aria-invalid="true" @enderror>
                                <button type="button" class="absolute inset-y-0 right-1 inline-flex w-11 items-center justify-center rounded-lg text-slate-500 hover:text-slate-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-700" data-password-toggle="password" aria-controls="password" aria-pressed="false" aria-label="Lihat password" title="Lihat password">
                                    <x-icon name="eye" class="h-5 w-5" data-eye-open/>
                                    <x-icon name="eye-off" class="hidden h-5 w-5" data-eye-closed/>
                                </button>
                            </div>
                            <div id="password-description">
                                <p class="field-hint">Minimal 8 karakter, dengan huruf besar, huruf kecil, angka, dan tanda baca.</p>
                                @error('password')<p class="field-error" role="alert">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mt-9 border-t border-slate-200 pt-8">
                    <legend class="mb-2 text-base font-semibold text-slate-950">Berkas verifikasi</legend>
                    <p class="mb-5 text-sm leading-6 text-slate-500">Format JPG atau PNG, masing-masing maksimal 2 MB.</p>
                    <div class="grid gap-5 sm:grid-cols-2 sm:gap-6">
                        <x-field name="foto_stnghBadan" label="Foto setengah badan" type="file" required accept="image/jpeg,image/png" hint="Foto digunakan pada kartu anggota." class="file:mr-3 file:rounded file:border-0 file:bg-zinc-100 file:px-3 file:py-1 file:text-sm file:text-zinc-700"/>
                        <x-field name="foto_KTM" label="Foto KTM" type="file" required accept="image/jpeg,image/png" hint="Pastikan nama dan NIM terbaca. Hanya petugas yang dapat mengakses KTM." class="file:mr-3 file:rounded file:border-0 file:bg-zinc-100 file:px-3 file:py-1 file:text-sm file:text-zinc-700"/>
                    </div>
                    @if($errors->any())
                        <p class="mt-4 text-sm text-amber-800" role="status">Pilih ulang kedua berkas foto sebelum mengirim formulir kembali.</p>
                    @endif
                </fieldset>

                <fieldset class="mt-9 border-t border-slate-200 pt-8">
                    <legend class="mb-2 text-base font-semibold text-slate-950">Verifikasi keamanan</legend>
                    <p class="mb-4 text-sm leading-6 text-slate-500">Jawab soal berikut untuk membantu mencegah pendaftaran otomatis.</p>
                    <label for="captcha" class="field-label">Berapa hasil {{ $captchaLeft }} + {{ $captchaRight }}?</label>
                    <input id="captcha" name="captcha" type="number" inputmode="numeric" min="2" max="18" required autocomplete="off" class="field max-w-xs" @if($errors->has('captcha')) aria-invalid="true" aria-describedby="captcha-error" @endif>
                    @error('captcha')<p id="captcha-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                </fieldset>

                <div class="mt-7">
                    <label for="persetujuan_qr" class="flex cursor-pointer items-start gap-3 text-sm leading-6 text-slate-700">
                        <input id="persetujuan_qr" name="persetujuan_qr" type="checkbox" value="1" required @checked(old('persetujuan_qr')) class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-sky-800 focus:ring-sky-700">
                        <span>Saya memahami bahwa, jika disetujui, nama, foto, NIM, program studi, fakultas, tanggal terbit, dan status anggota dapat dilihat publik saat QR kartu dipindai.</span>
                    </label>
                    @error('persetujuan_qr')<p class="field-error mt-2" role="alert">{{ $message }}</p>@enderror
                </div>

                <div class="mt-9 flex flex-col justify-between gap-5 border-t border-slate-200 pt-7 sm:flex-row sm:items-center">
                    <p class="max-w-sm text-sm leading-6 text-slate-500">Kartu anggota tersedia setelah pendaftaran disetujui petugas.</p>
                    <button type="submit" class="btn-primary w-full shrink-0 sm:w-auto">Kirim pendaftaran</button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                Sudah mendaftar?
                <a href="{{ route('login') }}" class="text-link">Masuk untuk melihat status</a>
            </p>
        </div>
    </section>
</x-layout>
