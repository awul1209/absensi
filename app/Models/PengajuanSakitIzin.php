<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSakitIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_sakit_izin';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'karyawan_id_nik', // Ganti nama kolom sesuai migrasi
        'tipe_pengajuan',
        'tanggal_mulai',
        'tanggal_akhir',
        'alasan',
        'dokumen_bukti_path',
        'status_persetujuan',
        'disetujui_oleh_admin_id',
        'tanggal_persetujuan',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'tanggal_persetujuan' => 'datetime',
    ];

    /**
     * Relasi ke Karyawan yang mengajukan (menggunakan NIK sebagai kunci).
     */
    public function karyawan()
    {
        // Parameter kedua adalah foreign key di model ini (pengajuan_sakit_izin.karyawan_id_nik)
        // Parameter ketiga adalah owner key di model terkait (karyawans.nik)
        return $this->belongsTo(Karyawan::class, 'karyawan_id_nik', 'nik');
    }

    /**
     * Relasi ke Admin yang menyetujui/menolak.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh_admin_id', 'id');
    }
}