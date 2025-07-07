<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengaturanAbsensi;

class PengaturanAbsensiController extends Controller
{
    /**
     * Menampilkan halaman form pengaturan absensi.
     */
    public function index()
    {
        $pengaturan = PengaturanAbsensi::first();
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Menyimpan atau memperbarui data pengaturan absensi.
     */
    public function store(Request $request)
    {
        // 1. Validasi semua input dari form
        $request->validate([
            'lokasi_kantor' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
            
            // PERBAIKAN: Mengubah format validasi untuk menerima detik (H:i:s)
            'jam_masuk_mulai' => 'required|date_format:H:i,H:i:s',
            'jam_masuk_selesai' => 'required|date_format:H:i,H:i:s|after:jam_masuk_mulai',
            'batas_terlambat' => 'required|date_format:H:i,H:i:s',
            'jam_pulang_mulai' => 'required|date_format:H:i,H:i:s',
            'jam_pulang_selesai' => 'required|date_format:H:i,H:i:s|after:jam_pulang_mulai',
        ]);

        // 2. Gunakan updateOrCreate untuk efisiensi.
        PengaturanAbsensi::updateOrCreate(
            ['id' => 1], // Kunci untuk mencari
            $request->all() // Data untuk di-update atau di-create
        );

        // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses.
        return back()->with('success', 'Pengaturan absensi berhasil disimpan!');
    }
}