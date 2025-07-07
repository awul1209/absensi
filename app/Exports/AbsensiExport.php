<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon; // Pastikan ini ada di bagian atas file

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $absensis;

    public function __construct($absensis)
    {
        $this->absensis = $absensis;
    }

    public function collection()
    {
        return $this->absensis;
    }

    public function headings(): array
    {
        return [
            'ID Absensi',
            'Tanggal',
            'Nama Karyawan',
            'NIK Karyawan',
            'Status Harian',
            'Jam Masuk',
            'Status Masuk',
            'Keterlambatan (Menit)',
            'Foto Masuk',
            'Latitude Masuk',
            'Longitude Masuk',
            'Jam Pulang',
            'Foto Pulang',
            'Latitude Pulang',
            'Longitude Pulang',
        ];
    }

    public function map($absensi): array
    {
        // ==========================================================
        // PASTIKAN BARIS INI SESUAI, terutama bagian Carbon::parse()
        // ==========================================================
        return [
            $absensi->id,
            $absensi->tanggal ? Carbon::parse($absensi->tanggal)->format('d M Y') : '-', // Baris yang diperbaiki
            $absensi->karyawan->nama_lengkap ?? '-',
            $absensi->karyawan->nik ?? '-',
            $absensi->status ?? '-',
            $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i:s') : '-',
            $absensi->status_masuk ?? '-',
            $absensi->keterlambatan_menit ?? '0',
            $absensi->foto_masuk ? asset('storage/' . $absensi->foto_masuk) : '-',
            $absensi->latitude_masuk ?? '-',
            $absensi->longitude_masuk ?? '-',
            $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i:s') : '-',
            $absensi->foto_pulang ? asset('storage/' . $absensi->foto_pulang) : '-',
            $absensi->latitude_pulang ?? '-',
            $absensi->longitude_pulang ?? '-',
        ];
    }
}