<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'pending' => Anggota::where('status_verifikasi', 'pending')->count(),
            'totalAnggota' => Anggota::where('status_verifikasi', 'disetujui')->count(),
            'totalBuku' => Buku::count(),
            'stokHabis' => Buku::where('jumlah_stok', 0)->count(),
            'pendaftarTerbaru' => Anggota::with('user')->where('status_verifikasi', 'pending')->latest()->limit(5)->get(),
        ]);
    }
}
