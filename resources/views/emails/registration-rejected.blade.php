@extends('emails.layout')
@section('content')
<h1 style="font-size:24px;">Hasil verifikasi pendaftaran</h1>
<p>Halo {{ $anggota->user->nama }},</p>
<p>Pendaftaran anggota dengan NIM <strong>{{ $anggota->nim }}</strong> belum dapat disetujui.</p>
<p><strong>Alasan penolakan:</strong></p>
<p style="padding:16px; background:#fff6ee; white-space:pre-wrap;">{{ $anggota->alasan_penolakan }}</p>
<p>Hubungi petugas perpustakaan jika membutuhkan penjelasan lebih lanjut.</p>
<p><a href="{{ route('anggota.kartu') }}" style="color:#174c3c; font-weight:bold;">Lihat status pendaftaran</a></p>
@endsection
