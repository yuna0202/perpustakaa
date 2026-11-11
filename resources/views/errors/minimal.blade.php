<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title') | Perpustakaan Uniga</title>
    <style>
        body{margin:0;background:#0b111b;color:#fff;font:16px/1.6 "Segoe UI",system-ui,sans-serif;min-height:100vh;display:grid;place-items:center}
        main{box-sizing:border-box;width:min(100%,44rem);padding:3rem 2rem}small{font-size:.78rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:#a8ddfb}
        h1{font-family:Georgia,serif;font-size:clamp(2.5rem,6vw,4rem);font-weight:400;letter-spacing:-.04em;line-height:1.1}p{color:#cbd5e1;max-width:34rem}
        a{display:inline-flex;align-items:center;min-height:44px;box-sizing:border-box;margin-top:1rem;padding:.625rem 1.25rem;background:#a8ddfb;color:#0b111b;border-radius:.5rem;text-decoration:none;font-size:.875rem;font-weight:600}a:hover{background:#bae6fd}a:focus-visible{outline:2px solid #a8ddfb;outline-offset:4px}
    </style>
</head>
<body><main><small>Perpustakaan Universitas Gajayana</small><p>Error @yield('code')</p><h1>@yield('title')</h1><p>@yield('message')</p><a href="{{ url('/') }}">Kembali ke beranda</a></main></body>
</html>
