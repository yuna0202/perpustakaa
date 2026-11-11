<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Before tanggal_terbit existed, the approval decision updated t_anggota.updated_at.
        // Records without a later update retain NULL because their approval date is uncertain.
        DB::table('t_anggota')
            ->where('status_verifikasi', 'disetujui')
            ->whereNull('tanggal_terbit')
            ->whereColumn('updated_at', '>', 'created_at')
            ->update(['tanggal_terbit' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        // Do not erase issue dates: the original NULL values cannot be distinguished later.
    }
};
