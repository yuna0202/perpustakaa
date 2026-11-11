<?php

namespace Tests;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

abstract class LibraryTestCase extends TestCase
{
    use RefreshDatabase;

    private int $identity = 0;

    protected function user(string $role = 'mahasiswa'): User
    {
        $number = ++$this->identity;

        return User::create([
            'nama' => "Mahasiswa Uji {$number}",
            'email' => "uji{$number}@example.test",
            'password' => Hash::make('Password123!'),
            'role' => $role,
        ]);
    }

    protected function member(array $attributes = [], ?User $user = null): Anggota
    {
        $user ??= $this->user();

        return $user->anggota()->create(array_merge([
            'nim' => '2451'.str_pad((string) $user->id_user, 6, '0', STR_PAD_LEFT),
            'prodi' => 'Sistem Informasi',
            'angkatan' => '2024',
            'no_hp' => '081234567890',
            'foto_stnghBadan' => 'anggota/foto/foto-uji.jpg',
            'foto_KTM' => 'ktm/ktm-uji.jpg',
            'status_verifikasi' => 'pending',
            'QRcode' => null,
        ], $attributes));
    }

    protected function registration(array $attributes = []): array
    {
        return array_merge([
            'nama' => 'Nadia Mahasiswa',
            'nim' => '24510099',
            'email' => 'nadia@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'prodi' => 'Sistem Informasi',
            'angkatan' => '2024',
            'no_hp' => '081222334455',
            'foto_stnghBadan' => UploadedFile::fake()->image('portrait.jpg', 100, 150),
            'foto_KTM' => UploadedFile::fake()->image('ktm.png', 200, 120),
        ], $attributes);
    }

    protected function category(string $name = 'Teknologi Informasi'): Kategori
    {
        return Kategori::create(['nama_kategori' => $name]);
    }

    protected function bookData(Kategori $category, array $attributes = []): array
    {
        return array_merge([
            'id_kategori' => $category->id_kategori,
            'judul' => 'Pengantar Sistem Informasi',
            'pengarang' => 'Dewi Pustaka',
            'penerbit' => 'Penerbit Contoh',
            'isbn' => '9786020000001',
            'tahun_terbit' => '2024',
            'jumlah_stok' => 3,
            'lokasi_rak' => 'A-01',
            'tanggal_masuk' => '2025-01-01',
        ], $attributes);
    }

    protected function book(Kategori $category, array $attributes = []): Buku
    {
        return Buku::create($this->bookData($category, $attributes));
    }
}
