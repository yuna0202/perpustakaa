<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_anggota', function (Blueprint $table) {
            $table->string('fakultas', 100)->nullable();
            $table->timestamp('tanggal_terbit')->nullable();
            $table->unsignedSmallInteger('angkatan')->nullable()->change();
        });

        Schema::table('t_buku', function (Blueprint $table) {
            $table->string('cover_path')->nullable();
        });

        $programs = [
            'Fakultas Ekonomi dan Bisnis' => ['manajemen', 'akuntansi', 'ekonomi pembangunan'],
            'Fakultas Sosial dan Budaya' => ['bahasa inggris', 'ilmu komunikasi', 'psikologi'],
            'Fakultas Teknik dan Informatika' => ['sistem informasi', 'teknik mesin', 'teknik elektro', 'teknik industri'],
        ];
        foreach ($programs as $faculty => $studyPrograms) {
            foreach ($studyPrograms as $program) {
                DB::table('t_anggota')->whereRaw('LOWER(TRIM(prodi)) = ?', [$program])->update(['fakultas' => $faculty]);
            }
        }
    }

    public function down(): void
    {
        // Keep angkatan nullable: new registrations deliberately have no cohort.
        Schema::table('t_buku', fn (Blueprint $table) => $table->dropColumn('cover_path'));
        Schema::table('t_anggota', fn (Blueprint $table) => $table->dropColumn(['fakultas', 'tanggal_terbit']));
    }
};
