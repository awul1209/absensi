<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi; // Import model Absensi Anda
use App\Models\Karyawan; // Import model Karyawan Anda
use Carbon\Carbon; // Untuk memudahkan manipulasi tanggal/waktu
use Maatwebsite\Excel\Facades\Excel; // PASTIKAN BARIS INI ADA
use App\Exports\AbsensiExport;      // PASTIKAN BARIS INI ADA
use Barryvdh\DomPDF\Facade\Pdf;     // PASTIKAN BARIS INI ADA

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan tanggal awal dan akhir dari request, jika ada
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $karyawanId = $request->input('karyawan_id'); // Filter berdasarkan karyawan

        $query = Absensi::with('karyawan'); // Eager load relasi karyawan

        // Filter berdasarkan rentang tanggal
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        } else {
            // Jika endDate tidak diset, set secara default ke hari ini agar tidak mengambil data masa depan
            $endDate = Carbon::now()->toDateString();
            $query->whereDate('tanggal', '<=', $endDate);
        }

        // Filter berdasarkan karyawan
        if ($karyawanId) {
            $query->where('karyawan_id', $karyawanId);
        }

        // Urutkan berdasarkan tanggal terbaru dan jam masuk
        $absensis = $query->orderBy('tanggal', 'desc')
                          ->orderBy('jam_masuk', 'desc')
                          ->paginate(15); // Menggunakan pagination, 15 item per halaman

        // Dapatkan daftar semua karyawan untuk dropdown filter
        $karyawanOptions = Karyawan::orderBy('nama_lengkap')->get();

        return view('admin.absensi.index', compact('absensis', 'startDate', 'endDate', 'karyawanOptions', 'karyawanId'));
    }

     public function export(Request $request)
    {
        $type = $request->query('type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $karyawanId = $request->query('karyawan_id');

        $absensiQuery = Absensi::query()->with('karyawan');

        if ($startDate) {
            $absensiQuery->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $absensiQuery->whereDate('tanggal', '<=', $endDate);
        } else {
            // Default: tampilkan data hingga hari ini jika endDate tidak diset
            $endDate = Carbon::now()->toDateString();
            $absensiQuery->whereDate('tanggal', '<=', $endDate);
        }

        if ($karyawanId) {
            $absensiQuery->where('karyawan_id', $karyawanId);
        }

        $absensis = $absensiQuery->orderBy('tanggal', 'desc')->get();

        if ($type === 'excel') {
            $fileName = 'data_absensi_' . Carbon::now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new AbsensiExport($absensis), $fileName);
        } elseif ($type === 'pdf') {
            $filterInfo = [];
            if ($startDate) $filterInfo[] = 'Dari: ' . Carbon::parse($startDate)->format('d M Y');
            if ($endDate) $filterInfo[] = 'Sampai: ' . Carbon::parse($endDate)->format('d M Y');
            if ($karyawanId) {
                $karyawan = Karyawan::find($karyawanId);
                if ($karyawan) $filterInfo[] = 'Karyawan: ' . $karyawan->nama_lengkap;
            }

            $pdf = Pdf::loadView('admin.absensi.pdf_report', compact('absensis', 'filterInfo'));
            $fileName = 'data_absensi_' . Carbon::now()->format('Ymd_His') . '.pdf';
            return $pdf->download($fileName);
        }

        return redirect()->back()->with('error', 'Tipe export tidak valid.');
    }

    // Metode lain (show, export) bisa ditambahkan di sini jika diperlukan
}