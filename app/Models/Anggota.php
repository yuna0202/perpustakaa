<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggota extends Model
{
    protected $table = 't_anggota';
    protected $primaryKey = 'id_anggota';
    protected $fillable = ['id_user', 'nim', 'prodi', 'fakultas', 'angkatan', 'no_hp', 'foto_stnghBadan', 'foto_KTM', 'QRcode', 'status_verifikasi', 'alasan_penolakan', 'tanggal_terbit'];
    protected $hidden = ['foto_KTM', 'QRcode'];

    protected function casts(): array
    {
        return ['angkatan' => 'integer', 'tanggal_terbit' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
