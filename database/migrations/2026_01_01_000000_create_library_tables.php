<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_user', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa');
            $table->timestamps();
        });
        Schema::create('t_anggota', function (Blueprint $table) {
            $table->id('id_anggota');
            $table->foreignId('id_user')->unique()->constrained('t_user', 'id_user')->cascadeOnDelete();
            $table->string('nim', 30)->unique();
            $table->string('prodi', 100);
            $table->unsignedSmallInteger('angkatan');
            $table->string('no_hp', 20);
            $table->string('foto_stnghBadan');
            $table->string('foto_KTM');
            $table->string('QRcode', 64)->nullable()->unique();
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->default('pending')->index();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
        Schema::create('t_kategori', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100)->unique();
        });
        Schema::create('t_buku', function (Blueprint $table) {
            $table->id('id_buku');
            $table->foreignId('id_kategori')->constrained('t_kategori', 'id_kategori')->restrictOnDelete();
            $table->string('judul');
            $table->string('pengarang');
            $table->string('penerbit');
            $table->string('isbn', 32)->unique();
            $table->unsignedSmallInteger('tahun_terbit');
            $table->unsignedInteger('jumlah_stok');
            $table->string('lokasi_rak', 100);
            $table->date('tanggal_masuk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_buku');
        Schema::dropIfExists('t_kategori');
        Schema::dropIfExists('t_anggota');
        Schema::dropIfExists('t_user');
    }
};
