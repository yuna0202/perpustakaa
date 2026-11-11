@extends('emails.layout')
@section('content')
<h1 style="font-size:24px;">Pendaftaran baru menunggu verifikasi</h1>
<p>Mahasiswa berikut telah mengirim pendaftaran anggota:</p>
<p><strong>{{ $anggota->user->nama }}</strong><br>NIM: {{ $anggota->nim }}<br>Program studi: {{ $anggota->prodi }}</p>
<p>Silakan masuk ke dashboard admin untuk memeriksa data dan berkas mahasiswa sebelum menyetujui atau menolak pendaftaran.</p>
<p><a href="{{ route('admin.pendaftar.show', $anggota) }}" style="color:#174c3c; font-weight:bold;">Periksa pendaftaran</a></p>
@endsection
