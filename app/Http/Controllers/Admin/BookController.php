<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Http\Requests\CatalogFilterRequest;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BookController extends Controller
{
    public function index(CatalogFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('admin.buku.index', [
            'buku' => Buku::with('kategori')->filter($filters['q'] ?? null, $filters['kategori'] ?? null)
                ->orderByDesc('id_buku')->paginate(15)->withQueryString(),
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.buku.form', ['buku' => new Buku, 'kategori' => Kategori::orderBy('nama_kategori')->get()]);
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $this->saveBook(new Buku, $request->validated());

        return to_route('admin.buku.index')->with('success', 'Buku berhasil ditambahkan ke katalog.');
    }

    public function show(Buku $buku): RedirectResponse
    {
        return to_route('katalog.show', $buku);
    }

    public function edit(Buku $buku): View
    {
        return view('admin.buku.form', ['buku' => $buku, 'kategori' => Kategori::orderBy('nama_kategori')->get()]);
    }

    public function update(BookRequest $request, Buku $buku): RedirectResponse
    {
        $this->saveBook($buku, $request->validated());

        return to_route('admin.buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        $cover = $buku->cover_path;
        $buku->delete();
        $this->deleteCover($cover);

        return to_route('admin.buku.index')->with('success', 'Buku berhasil dihapus.');
    }

    private function saveBook(Buku $buku, array $data): void
    {
        $oldCover = $buku->cover_path;
        $newCover = null;
        $upload = $data['cover'] ?? null;
        $removeCover = (bool) ($data['remove_cover'] ?? false);
        unset($data['cover'], $data['remove_cover']);

        try {
            if ($upload instanceof UploadedFile) {
                $storedCover = $upload->store('buku/covers', 'public');
                if (! is_string($storedCover) || $storedCover === '') {
                    throw new \RuntimeException('Cover buku tidak dapat disimpan.');
                }
                $newCover = $storedCover;
                $data['cover_path'] = $newCover;
            } elseif ($removeCover) {
                $data['cover_path'] = null;
            }
            $buku->fill($data)->save();
        } catch (Throwable $exception) {
            $this->deleteCover($newCover);
            // Friendly errors also when a concurrent request wins after validation.
            if ($exception instanceof QueryException && Buku::where('isbn', $data['isbn'])->when($buku->exists, fn ($q) => $q->where('id_buku', '!=', $buku->id_buku))->exists()) {
                throw ValidationException::withMessages(['isbn' => 'ISBN sudah digunakan oleh buku lain.']);
            }
            if ($exception instanceof QueryException && ! Kategori::whereKey($data['id_kategori'])->exists()) {
                throw ValidationException::withMessages(['id_kategori' => 'Kategori sudah dihapus. Silakan pilih kategori lain.']);
            }
            throw $exception;
        }

        if ($oldCover && ($newCover || $removeCover)) {
            $this->deleteCover($oldCover);
        }
    }

    private function deleteCover(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'buku/covers/')) {
            return;
        }

        try {
            Storage::disk('public')->delete($path);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
