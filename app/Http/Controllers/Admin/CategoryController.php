<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.kategori.index', ['kategori' => Kategori::withCount('buku')->orderBy('nama_kategori')->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.kategori.form', ['kategori' => new Kategori]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->saveCategory(new Kategori, $request->validated());

        return to_route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('admin.kategori.form', compact('kategori'));
    }

    public function update(CategoryRequest $request, Kategori $kategori): RedirectResponse
    {
        $this->saveCategory($kategori, $request->validated());

        return to_route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->buku()->exists()) {
            return back()->with('error', 'Kategori masih digunakan oleh buku dan tidak dapat dihapus. Pindahkan atau hapus buku tersebut terlebih dahulu.');
        }
        try {
            $kategori->delete();
        } catch (QueryException $exception) {
            if ($kategori->buku()->exists()) {
                return back()->with('error', 'Kategori masih digunakan oleh buku dan tidak dapat dihapus.');
            }
            throw $exception;
        }

        return to_route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function saveCategory(Kategori $kategori, array $data): void
    {
        try {
            $kategori->fill($data)->save();
        } catch (QueryException $exception) {
            if (Kategori::where('nama_kategori', $data['nama_kategori'])->when($kategori->exists, fn ($q) => $q->where('id_kategori', '!=', $kategori->id_kategori))->exists()) {
                throw ValidationException::withMessages(['nama_kategori' => 'Nama kategori sudah digunakan.']);
            }
            throw $exception;
        }
    }
}
