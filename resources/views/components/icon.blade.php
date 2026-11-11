@props(['name' => 'book'])
<svg {{ $attributes->merge(['class' => 'h-5 w-5 shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@switch($name)
    @case('arrow') <path d="M4 12h16m-6-6 6 6-6 6"/> @break
    @case('chevron') <path d="m9 5 7 7-7 7"/> @break
    @case('close') <path d="m6 6 12 12M6 18 18 6"/> @break
    @case('card') <rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8" cy="10" r="1.5"/><path d="M5.5 15a2.5 2.5 0 0 1 5 0M14 10h4m-4 4h3"/> @break
    @case('edit') <path d="m16 3 5 5-12 12-6 1 1-6L16 3Zm-2 2 5 5"/> @break
    @case('trash') <path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/> @break
    @case('search') <circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/> @break
    @case('eye') <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/> @break
    @case('eye-off') <path d="m3 3 18 18M10.6 5.1A11.5 11.5 0 0 1 12 5c6.5 0 10 7 10 7a16 16 0 0 1-3.1 3.9M6.1 6.1C3.4 8 2 12 2 12s3.5 7 10 7c1.4 0 2.7-.3 3.8-.8M10 10a3 3 0 0 0 4 4"/> @break
    @case('users') <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/><circle cx="9" cy="7" r="4"/> @break
    @case('grid') <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/> @break
    @case('category') <path d="M3 7V4a1 1 0 0 1 1-1h6l11 11-7 7L3 10V7Z"/><circle cx="7" cy="7" r="1"/> @break
    @case('check') <path d="m5 12 4 4L19 6"/> @break
    @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
    @case('logout') <path d="M9 4H4v16h5m0-8h12m-4-4 4 4-4 4"/> @break
    @case('plus') <path d="M12 5v14M5 12h14"/> @break
    @case('menu') <path d="M4 6h16M4 12h16M4 18h16"/> @break
    @case('print') <path d="M6 9V3h12v6M6 17H3V9h18v8h-3M6 14h12v7H6zM17 11h1"/> @break
    @case('location') <path d="M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/> @break
    @case('shield') <path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/> @break
    @default <path d="M12 5c-3-2-7-2-10-1v15c3-1 7-1 10 1m0-15c3-2 7-2 10-1v15c-3-1-7-1-10 1V5Z"/>
@endswitch
</svg>
