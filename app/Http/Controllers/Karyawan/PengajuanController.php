<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanSakitIzin; // Pastikan model ini diimpor
use App\Models\Karyawan; // Pastikan model ini diimpor
use Carbon\Carbon;

class PengajuanController extends Controller
{
    public function create()
    {
        // Dapatkan data karyawan yang sedang login
        // Pastikan relasi user()->karyawan() sudah terdefinisi dan berfungsi
        $karyawan = Auth::user()->karyawan; 

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan Anda tidak ditemukan.');
        }

        return view('karyawan.pengajuan.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        // Dapatkan data karyawan yang sedang login
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan Anda tidak ditemukan.');
        }

        // Validasi input
    $request->validate([
        'tipe_pengajuan' => 'required|in:sakit,izin,cuti',
        'tanggal_mulai' => 'required|date',
        'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        'alasan' => 'required|string|max:500',
        'dokumen_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
    ]);

    $dokumenPath = null;
    if ($request->hasFile('dokumen_bukti')) {
        // =========== PERBAIKAN DI SINI ===========
        // Secara eksplisit gunakan 'public' disk
        // Path yang dikembalikan sudah relatif terhadap 'storage/app/public/'
        $dokumenPath = $request->file('dokumen_bukti')->store('dokumen_pengajuan', 'public');

        // TIDAK PERLU lagi str_replace('public/', '', ...) karena path sudah bersih
        // $dokumenPath = str_replace('public/', '', $dokumenPath); // BARIS INI DIHAPUS
        // ===========================================
    }

    // Buat pengajuan baru
    PengajuanSakitIzin::create([
        'karyawan_id_nik' => $karyawan->nik,
        'tipe_pengajuan' => $request->tipe_pengajuan,
        'tanggal_mulai' => $request->tanggal_mulai,
        'tanggal_akhir' => $request->tanggal_akhir,
        'alasan' => $request->alasan,
        'dokumen_bukti_path' => $dokumenPath, // $dokumenPath sudah benar sekarang
        'status_persetujuan' => 'pending',
    ]);

    return redirect()->route('karyawan.pengajuan.riwayat')->with('success', 'Pengajuan Anda berhasil diajukan. Menunggu persetujuan admin.');
    }

    public function riwayat()
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan Anda tidak ditemukan.');
        }

        // Ambil riwayat pengajuan karyawan ini
        $pengajuans = PengajuanSakitIzin::where('karyawan_id_nik', $karyawan->nik)
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10); // Pagination

        return view('karyawan.pengajuan.riwayat', compact('pengajuans', 'karyawan'));
    }
}