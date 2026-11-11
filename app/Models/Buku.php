<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Buku extends Model
{
    protected $table = 't_buku';
    protected $primaryKey = 'id_buku';
    public $timestamps = false;
    protected $fillable = ['id_kategori', 'judul', 'pengarang', 'penerbit', 'isbn', 'tahun_terbit', 'jumlah_stok', 'lokasi_rak', 'tanggal_masuk', 'cover_path'];

    protected function casts(): array
    {
        return ['tanggal_masuk' => 'date', 'tahun_terbit' => 'integer', 'jumlah_stok' => 'integer'];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function scopeFilter(Builder $query, ?string $search, ?string $category): void
    {
        $query->when($search !== null && $search !== '', function (Builder $query) use ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('judul', 'like', '%'.$search.'%')
                    ->orWhere('pengarang', 'like', '%'.$search.'%')
                    ->orWhere('isbn', 'like', '%'.$search.'%');
            });
        })->when($category, fn (Builder $query, string $category) => $query->where('id_kategori', $category));
    }
}
