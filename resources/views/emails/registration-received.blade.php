@extends('emails.layout')
@section('content')
<h1 style="font-size:24px;">Pendaftaran Anda telah diterima</h1>
<p>Halo {{ $anggota->user->nama }},</p>
<p>Pendaftaran anggota dengan NIM <strong>{{ $anggota->nim }}</strong> berhasil disimpan dan sedang menunggu verifikasi petugas.</p>
<p>Kartu anggota dapat dilihat dan dicetak setelah pendaftaran disetujui. Hasil verifikasi akan dikirim melalui email.</p>
<p><a href="{{ route('anggota.kartu') }}" style="color:#174c3c; font-weight:bold;">Lihat status pendaftaran</a></p>
@endsection
