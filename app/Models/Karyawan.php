<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    /**
     * PERBAIKAN: Mendaftarkan semua kolom yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'posisi',
        'nomor_telepon',
        'foto_wajah',
        'foto_referensi',
        'nik',
        'jenis_kelamin',
        'tahun_masuk',
        'alamat',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model User.
     * Ini adalah kebalikan dari relasi hasOne di model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
