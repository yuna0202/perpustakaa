@props(['status'])
@if($status === 'disetujui')
    <span class="badge-green">Disetujui</span>
@elseif($status === 'ditolak')
    <span class="badge-red">Ditolak</span>
@else
    <span class="badge-amber">Menunggu verifikasi</span>
@endif
