<x-layout title="Masuk">
    <section class="container-page py-10 sm:py-16">
        <div class="grid overflow-hidden rounded-2xl border border-slate-200 bg-white lg:min-h-[540px] lg:grid-cols-[0.95fr_1.05fr]">
            <div class="flex flex-col justify-between bg-slate-950 px-7 py-6 text-white sm:px-10 sm:py-12 lg:px-12">
                <div>
                    <p class="text-sm font-medium text-sky-300">Akses anggota dan petugas</p>
                    <h1 class="mt-4 max-w-md font-serif text-3xl leading-[1.08] tracking-tight sm:mt-6 sm:text-5xl">Layanan perpustakaan dalam satu akun.</h1>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-slate-300 sm:mt-6 sm:leading-7">Masuk untuk melihat status pendaftaran, membuka kartu anggota, atau mengelola koleksi buku.</p>
                </div>
                <a href="{{ route('katalog.index') }}" class="mt-4 inline-flex min-h-11 items-center gap-2 self-start text-sm font-medium text-sky-300 hover:text-white sm:mt-8">
                    Jelajahi katalog <x-icon name="arrow" class="h-4 w-4"/>
                </a>
            </div>

            <div class="flex items-center px-7 py-10 sm:px-10 sm:py-12 lg:px-16">
                <div class="w-full max-w-md">
                    <h2 class="text-2xl font-semibold tracking-tight text-slate-950">Masuk ke akun</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan email dan password yang didaftarkan.</p>

                    <form action="{{ route('login.store') }}" method="POST" class="mt-8 space-y-5">
                        @csrf
                        <x-field name="email" label="Email" type="email" required autocomplete="username" placeholder="nama@email.com" maxlength="255" spellcheck="false"/>
                        <div>
                            <label for="password" class="field-label">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" class="field pr-14 @error('password') border-red-500 @enderror" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                                <button type="button" class="absolute inset-y-0 right-1 inline-flex w-11 items-center justify-center rounded-lg text-slate-500 hover:text-slate-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-700" data-password-toggle="password" aria-controls="password" aria-pressed="false" aria-label="Lihat password" title="Lihat password">
                                    <x-icon name="eye" class="h-5 w-5" data-eye-open/>
                                    <x-icon name="eye-off" class="hidden h-5 w-5" data-eye-closed/>
                                </button>
                            </div>
                            @error('password')<p id="password-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-primary mt-2 w-full">Masuk</button>
                    </form>

                    <p class="mt-7 border-t border-slate-200 pt-6 text-sm text-slate-600">
                        Belum memiliki akun? <a href="{{ route('daftar') }}" class="text-link">Daftar anggota</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-layout>
