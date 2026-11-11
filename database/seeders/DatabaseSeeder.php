<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Re-running seed must not reset an existing administrator password.
        $adminPassword = config('library.initial_admin_password');
        if (is_string($adminPassword) && $adminPassword !== '') {
            User::firstOrCreate(['email' => 'admin@gajayana.ac.id'], [
                'nama' => 'Admin Perpustakaan',
                'password' => $adminPassword,
                'role' => 'admin',
            ]);
        }

        $collections = [
            'Teknologi Informasi' => ['Pengantar Sistem Informasi', 'Dasar Pemrograman Web', 'Perancangan Basis Data', 'Analisis dan Desain Sistem'],
            'Ekonomi & Bisnis' => ['Pengantar Manajemen', 'Dasar Akuntansi Keuangan', 'Kewirausahaan untuk Mahasiswa', 'Ekonomi Mikro Terapan'],
            'Sastra & Bahasa' => ['Bahasa Indonesia Akademik', 'Pengantar Linguistik', 'Apresiasi Sastra Nusantara', 'English for Academic Reading'],
            'Teknik' => ['Matematika Teknik Dasar', 'Pengantar Teknik Industri', 'Dasar Elektronika', 'Statistika Terapan'],
            'Ilmu Sosial' => ['Metode Penelitian Sosial', 'Pengantar Ilmu Komunikasi', 'Etika Profesi', 'Pendidikan Kewarganegaraan'],
        ];
        $index = 0;
        foreach ($collections as $category => $titles) {
            $kategori = Kategori::firstOrCreate(['nama_kategori' => $category]);
            foreach ($titles as $title) {
                $index++;
                Buku::firstOrCreate(['isbn' => '978602000'.str_pad((string) $index, 4, '0', STR_PAD_LEFT)], [
                    'id_kategori' => $kategori->id_kategori,
                    'judul' => $title,
                    'pengarang' => ['Tim Akademik Nusantara', 'Dewi Puspita', 'Arif Wicaksono', 'Ratna Pramesti'][($index - 1) % 4],
                    'penerbit' => 'Penerbit Contoh Akademika',
                    'tahun_terbit' => 2020 + ($index % 5),
                    'jumlah_stok' => $index % 5 === 0 ? 0 : 2 + ($index % 7),
                    'lokasi_rak' => 'Rak '.chr(65 + intdiv($index - 1, 4)).'-'.str_pad((string) (($index - 1) % 4 + 1), 2, '0', STR_PAD_LEFT),
                    'tanggal_masuk' => '2026-01-15',
                ]);
            }
        }
    }
}
