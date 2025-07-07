<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'karyawan_id', // Ini harus menyimpan NIK sekarang
        'tanggal',
        'status',
        'jam_masuk',
        'status_masuk',
        'foto_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'keterlambatan_menit',
        'jam_pulang',
        'foto_pulang',
        'latitude_pulang',
        'longitude_pulang',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'latitude_masuk' => 'decimal:7',
        'longitude_masuk' => 'decimal:7',
        'latitude_pulang' => 'decimal:7',
        'longitude_pulang' => 'decimal:7',
        'keterlambatan_menit' => 'integer',
    ];

    /**
     * Relasi ke Karyawan (menggunakan NIK sebagai kunci).
     */
    public function karyawan()
    {
        // Parameter kedua adalah foreign key di model ini (absensi.karyawan_id)
        // Parameter ketiga adalah owner key di model terkait (karyawans.nik)
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'nik');
    }

    // Accessor untuk mendapatkan waktu masuk sebagai Carbon instance, jika diperlukan
    public function getJamMasukCarbonAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_masuk) : null;
    }

    // Accessor untuk mendapatkan waktu pulang sebagai Carbon instance, jika diperlukan
    public function getJamPulangCarbonAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_pulang) : null;
    }
}