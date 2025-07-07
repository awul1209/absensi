<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanSakitIzin;
use App\Models\Absensi; // Digunakan saat menyetujui
use Carbon\Carbon;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->input('status', 'pending'); // Filter default: pending
        $tipeFilter = $request->input('tipe');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PengajuanSakitIzin::with('karyawan', 'admin') // Eager load relasi
                                ->orderBy('created_at', 'desc');

        if ($statusFilter && $statusFilter != 'all') {
            $query->where('status_persetujuan', $statusFilter);
        }
        if ($tipeFilter && $tipeFilter != 'all') {
            $query->where('tipe_pengajuan', $tipeFilter);
        }
        if ($startDate) {
            $query->whereDate('tanggal_mulai', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_akhir', '<=', $endDate);
        }

        $pengajuans = $query->paginate(15);

        return view('admin.pengajuan.index', compact('pengajuans', 'statusFilter', 'tipeFilter', 'startDate', 'endDate'));
    }

    public function show(PengajuanSakitIzin $pengajuanSakitIzin)
    {
        return view('admin.pengajuan.show', compact('pengajuanSakitIzin'));
    }

    public function approve(Request $request, PengajuanSakitIzin $pengajuanSakitIzin)
    {
        if ($pengajuanSakitIzin->status_persetujuan != 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah tidak dalam status menunggu persetujuan.');
        }

        // Update status pengajuan
        $pengajuanSakitIzin->status_persetujuan = 'approved';
        $pengajuanSakitIzin->disetujui_oleh_admin_id = Auth::id(); // Admin yang sedang login
        $pengajuanSakitIzin->tanggal_persetujuan = Carbon::now();
        $pengajuanSakitIzin->save();

        // ============ LOGIKA PENTING: UPDATE TABEL ABSENSI ============
        $currentDate = Carbon::parse($pengajuanSakitIzin->tanggal_mulai);
        $endDate = Carbon::parse($pengajuanSakitIzin->tanggal_akhir);

        while ($currentDate->lte($endDate)) {
            Absensi::updateOrCreate(
                [
                    'karyawan_id' => $pengajuanSakitIzin->karyawan_id_nik, // Menggunakan NIK
                    'tanggal' => $currentDate->toDateString(),
                ],
                [
                    'status' => ucfirst($pengajuanSakitIzin->tipe_pengajuan), // 'Sakit', 'Izin', 'Cuti'
                    'jam_masuk' => null, // Tidak ada absensi fisik
                    'jam_pulang' => null,
                    'status_masuk' => $pengajuanSakitIzin->tipe_pengajuan, // 'sakit', 'izin', 'cuti'
                    'keterangan_sakit_izin' => $pengajuanSakitIzin->alasan, // Kolom ini harus ada di tabel absensis
                    'dokumen_bukti' => $pengajuanSakitIzin->dokumen_bukti_path, // Kolom ini harus ada di tabel absensis
                ]
            );
            $currentDate->addDay();
        }
        // =============================================================

        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil disetujui dan absensi diperbarui.');
    }

    public function reject(Request $request, PengajuanSakitIzin $pengajuanSakitIzin)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        if ($pengajuanSakitIzin->status_persetujuan != 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah tidak dalam status menunggu persetujuan.');
        }

        // Update status pengajuan
        $pengajuanSakitIzin->status_persetujuan = 'rejected';
        $pengajuanSakitIzin->disetujui_oleh_admin_id = Auth::id(); // Admin yang sedang login
        $pengajuanSakitIzin->tanggal_persetujuan = Carbon::now();
        $pengajuanSakitIzin->alasan_penolakan = $request->alasan_penolakan;
        $pengajuanSakitIzin->save();

        // Tidak ada update ke tabel absensi jika ditolak

        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil ditolak.');
    }
}