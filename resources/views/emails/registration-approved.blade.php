@extends('emails.layout')
@section('content')
<h1 style="font-size:24px;">Selamat, pendaftaran Anda disetujui</h1>
<p>Halo {{ $anggota->user->nama }},</p>
<p>Anda telah terdaftar sebagai anggota Perpustakaan Universitas Gajayana Malang dengan NIM <strong>{{ $anggota->nim }}</strong>.</p>
<p>Silakan masuk ke akun Anda untuk melihat dan mencetak kartu anggota beserta kode QR verifikasi.</p>
<p><a href="{{ route('anggota.kartu') }}" style="color:#174c3c; font-weight:bold;">Buka kartu anggota</a></p>
@endsection
