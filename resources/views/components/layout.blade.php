@props(['title' => 'Perpustakaan', 'admin' => false])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Katalog buku dan pendaftaran anggota Perpustakaan Universitas Gajayana Malang.">
    <meta name="theme-color" content="#0b111b">
    <meta name="color-scheme" content="light">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" sizes="32x32" href="{{ asset('favicon.ico') }}">
    <title>{{ $title }} | Perpustakaan Uniga</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>
    @if($admin)
        <aside class="admin-sidebar no-print">
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <x-brand-mark/>
                <span><span class="brand-name">Perpustakaan</span><span class="brand-caption">Universitas Gajayana</span></span>
            </a>
            <p class="mb-3 mt-10 px-3 text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Pengelolaan</p>
            <nav class="space-y-1" aria-label="Navigasi admin">
                @foreach([
                    ['admin.dashboard', 'admin.dashboard', 'grid', 'Dashboard'],
                    ['admin.pendaftar.index', 'admin.pendaftar.*', 'users', 'Pendaftar anggota'],
                    ['admin.buku.index', 'admin.buku.*', 'book', 'Koleksi buku'],
                    ['admin.kategori.index', 'admin.kategori.*', 'category', 'Kategori buku'],
                ] as [$link, $pattern, $icon, $label])
                    <a href="{{ route($link) }}" class="admin-nav-link {{ request()->routeIs($pattern) ? 'admin-nav-active' : '' }}" @if(request()->routeIs($pattern)) aria-current="page" @endif>
                        <x-icon :name="$icon" class="h-[18px] w-[18px]"/>
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
            <div class="mt-auto border-t border-white/15 pt-4">
                <a href="{{ route('katalog.index') }}" class="admin-nav-link"><x-icon name="arrow" class="h-[18px] w-[18px]"/> Katalog publik</a>
            </div>
        </aside>
        <div class="lg:pl-60">
            <header class="admin-topbar no-print">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" data-menu-toggle aria-controls="admin-mobile-nav" aria-expanded="false" class="icon-button lg:hidden" aria-label="Buka navigasi admin"><x-icon name="menu"/></button>
                    <span class="hidden text-sm text-zinc-500 sm:block">Administrasi</span>
                    <x-icon name="chevron" class="hidden h-3.5 w-3.5 text-zinc-400 sm:block"/>
                    <span class="truncate text-sm font-medium">{{ $title }}</span>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="hidden max-w-48 truncate text-sm text-zinc-600 sm:block">{{ auth()->user()->nama }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="icon-button" aria-label="Keluar"><x-icon name="logout" class="h-[18px] w-[18px]"/></button>
                    </form>
                </div>
            </header>
            <nav id="admin-mobile-nav" class="mobile-menu no-print lg:hidden" aria-label="Navigasi admin seluler" hidden>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.pendaftar.index') }}">Pendaftar anggota</a>
                <a href="{{ route('admin.buku.index') }}">Koleksi buku</a>
                <a href="{{ route('admin.kategori.index') }}">Kategori buku</a>
                <a href="{{ route('katalog.index') }}">Katalog publik</a>
            </nav>
            <main id="main-content" class="admin-main" tabindex="-1">
                <x-flash/>
                {{ $slot }}
            </main>
        </div>
    @else
        <header class="public-header no-print">
            <div class="container-page flex min-h-[76px] items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="brand-link" aria-label="Beranda Perpustakaan Universitas Gajayana">
                    <x-brand-mark/>
                    <span><span class="brand-name">Perpustakaan</span><span class="brand-caption">Universitas Gajayana Malang</span></span>
                </a>
                <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                    <a href="{{ route('home') }}" class="public-nav-link {{ request()->routeIs('home') ? 'public-nav-active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
                    <a href="{{ route('katalog.index') }}" class="public-nav-link {{ request()->routeIs('katalog.*') ? 'public-nav-active' : '' }}" @if(request()->routeIs('katalog.*')) aria-current="page" @endif>Katalog buku</a>
                    @guest
                        <a href="{{ route('daftar') }}" class="public-nav-link {{ request()->routeIs('daftar') ? 'public-nav-active' : '' }}" @if(request()->routeIs('daftar')) aria-current="page" @endif>Pendaftaran anggota</a>
                        <a href="{{ route('login') }}" class="btn-primary ml-5">Masuk</a>
                    @else
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('anggota.kartu') }}" class="btn-primary ml-5">{{ auth()->user()->role === 'admin' ? 'Dashboard' : 'Kartu anggota' }}</a>
                        <form action="{{ route('logout') }}" method="POST" class="ml-2">
                            @csrf
                            <button type="submit" class="icon-button" aria-label="Keluar"><x-icon name="logout" class="h-[18px] w-[18px]"/></button>
                        </form>
                    @endguest
                </nav>
                <button type="button" data-menu-toggle aria-controls="public-mobile-nav" aria-expanded="false" class="icon-button lg:hidden" aria-label="Buka menu"><x-icon name="menu"/></button>
            </div>
            <nav id="public-mobile-nav" class="mobile-menu lg:hidden" aria-label="Navigasi seluler" hidden>
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('katalog.index') }}">Katalog buku</a>
                @guest
                    <a href="{{ route('daftar') }}">Pendaftaran anggota</a>
                    <a href="{{ route('login') }}">Masuk</a>
                @else
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('anggota.kartu') }}">{{ auth()->user()->role === 'admin' ? 'Dashboard' : 'Kartu anggota' }}</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left">Keluar</button>
                    </form>
                @endguest
            </nav>
        </header>
        <main id="main-content" class="min-h-[72vh]" tabindex="-1">
            <div class="container-page"><x-flash/></div>
            {{ $slot }}
        </main>
        <footer class="site-footer no-print">
            <div class="container-page flex flex-col justify-between gap-5 py-8 sm:flex-row sm:items-center">
                <p class="text-xs leading-5">Perpustakaan Universitas Gajayana Malang</p>
                <div class="flex flex-wrap items-center gap-6 text-xs">
                    <a href="{{ route('katalog.index') }}" class="footer-link">Katalog buku</a>
                    @guest<a href="{{ route('daftar') }}" class="footer-link">Pendaftaran anggota</a>@endguest
                    <span>{{ date('Y') }}</span>
                </div>
            </div>
        </footer>
    @endif
</body>
</html>
