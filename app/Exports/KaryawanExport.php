<?php

namespace App\Exports;

use App\Models\Karyawan; // Pastikan untuk mengimpor model Karyawan Anda
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon; // Untuk format tanggal jika diperlukan

class KaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahunMasuk;

    public function __construct($tahunMasuk = null)
    {
        $this->tahunMasuk = $tahunMasuk;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Karyawan::query();

        if ($this->tahunMasuk) {
            $query->where('tahun_masuk', $this->tahunMasuk);
        }

        // Eager load relasi user jika email diperlukan
        return $query->with('user')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Lengkap',
            'NIK',
            'Posisi',
            'Email',
            'Tahun Masuk',
        ];
    }

    /**
     * @var Karyawan $karyawan
     */
    public function map($karyawan): array
    {
        return [
            $karyawan->id,
            $karyawan->nama_lengkap,
            $karyawan->nik,
            $karyawan->posisi,
            $karyawan->user->email ?? '-', // Tangani kasus di mana user mungkin tidak terhubung
            $karyawan->tahun_masuk,
        ];
    }
}