<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanAbsensi extends Model
{
    use HasFactory;

    /**
     * PERBAIKAN: Memberitahu Laravel nama tabel yang benar untuk model ini.
     */
    protected $table = 'pengaturan_absensi';

    /**
     * Mencegah error MassAssignmentException.
     * Pastikan semua kolom dari form ada di sini.
     */
    protected $fillable = [
        'lokasi_kantor',
        'latitude',
        'longitude',
        'radius',
        'jam_masuk_mulai',
        'jam_masuk_selesai',
        'batas_terlambat',
        'jam_pulang_mulai',
        'jam_pulang_selesai',
    ];
}
