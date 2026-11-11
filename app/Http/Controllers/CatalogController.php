<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogFilterRequest;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'totalBuku' => Buku::count(),
            'totalKategori' => Kategori::count(),
            'bukuTerbaru' => Buku::with('kategori')->orderByDesc('tanggal_masuk')->orderByDesc('id_buku')->limit(6)->get(),
        ]);
    }

    public function index(CatalogFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('katalog.index', [
            'buku' => Buku::with('kategori')->filter($filters['q'] ?? null, $filters['kategori'] ?? null)
                ->orderBy('judul')->paginate(12)->withQueryString(),
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function show(Buku $buku): View
    {
        return view('katalog.show', ['buku' => $buku->load('kategori')]);
    }
}
