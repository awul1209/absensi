<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil Karyawan Anda tidak ditemukan.');
        }
        
        if (!$karyawan->foto_referensi) {
            return redirect()->route('karyawan.profil.pendaftaran-wajah')->with('warning', 'Anda harus mendaftarkan wajah terlebih dahulu sebelum melakukan absensi.');
        }

        // PERBAIKAN 2: Cek apakah NIK sudah diisi
        if (empty($karyawan->nik)) {
            return redirect()->route('karyawan.dashboard')->with('error', 'NIK Anda belum terdaftar. Harap hubungi Admin.');
        }

        $today = Carbon::today()->toDateString();
        // PERBAIKAN 3: Cek absensi berdasarkan NIK
        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->nik)->where('tanggal', $today)->first();
        
        // PERBAIKAN 4: Logika penolakan jika absen sudah lengkap
        if ($absensiHariIni && $absensiHariIni->jam_pulang) {
            return redirect()->route('karyawan.dashboard')->with('info', 'Anda sudah menyelesaikan absensi hari ini (masuk dan pulang).');
        }

        $pengaturan = PengaturanAbsensi::first();
        if (!$pengaturan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Pengaturan absensi belum diatur oleh Admin.');
        }

        $fotoReferensiUrl = asset('storage/' . $karyawan->foto_referensi);

        return view('karyawan.absen', compact('pengaturan', 'fotoReferensiUrl'));
    }

    private function prosesAbsensi(Request $request, $jenisAbsen)
    {
        $request->validate([
            'image' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $pengaturan = PengaturanAbsensi::first();
        if (!$pengaturan || !$pengaturan->latitude) {
            return response()->json(['message' => 'Pengaturan lokasi belum diatur oleh admin.'], 500);
        }

        $jarak = $this->haversineDistance($pengaturan->latitude, $pengaturan->longitude, $request->latitude, $request->longitude);

        if ($jarak > $pengaturan->radius) {
            return response()->json(['message' => 'Anda berada di luar radius absen. Jarak Anda: ' . round($jarak) . ' meter.'], 403);
        }

        $karyawan = Auth::user()->karyawan;
        $now = Carbon::now();
        $today = $now->toDateString();
        $time = $now->toTimeString();

        if ($jenisAbsen === 'masuk') {
            // ... (logika validasi jam masuk tidak berubah) ...
            $jamMasukMulai = Carbon::createFromTimeString($pengaturan->jam_masuk_mulai);
            $jamMasukSelesai = Carbon::createFromTimeString($pengaturan->jam_masuk_selesai);
            if ($now->isBefore($jamMasukMulai) || $now->isAfter($jamMasukSelesai)) {
                return response()->json(['message' => 'Absen masuk hanya bisa dilakukan antara ' . $jamMasukMulai->format('H:i') . ' - ' . $jamMasukSelesai->format('H:i') . '.'], 400);
            }
            if (Absensi::where('karyawan_id', $karyawan->nik)->where('tanggal', $today)->exists()) {
                return response()->json(['message' => 'Anda sudah melakukan absen masuk hari ini.'], 400);
            }

            // ... (logika keterlambatan tidak berubah) ...
            $batasTerlambat = Carbon::createFromTimeString($pengaturan->batas_terlambat);
            $keterlambatan = 0;
            $status = 'tepat_waktu';
            if ($now->isAfter($batasTerlambat)) {
                $status = 'terlambat';
                $keterlambatan = $batasTerlambat->diffInMinutes($now);
            }
            
            $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
            $fileName = 'absen-masuk/' . $karyawan->nik . '-' . $today . '.jpg'; // Nama file sudah menggunakan NIK
            Storage::disk('public')->put($fileName, $imgData);

            // PERBAIKAN 1: Menyimpan NIK ke dalam karyawan_id
            Absensi::create([
                'karyawan_id' => $karyawan->nik, 
                'tanggal' => $today, 'jam_masuk' => $time,
                'status_masuk' => $status, 'status' => 'Hadir',
                'latitude_masuk' => $request->latitude, 'longitude_masuk' => $request->longitude,
                'keterlambatan_menit' => $keterlambatan, 'foto_masuk' => $fileName,
            ]);
            return response()->json(['message' => 'Absen masuk berhasil! Anda tercatat ' . str_replace('_', ' ', $status) . '.'], 200);
        }

        if ($jenisAbsen === 'pulang') {
            // ... (logika validasi jam pulang tidak berubah) ...
             $jamPulangMulai = Carbon::createFromTimeString($pengaturan->jam_pulang_mulai);
            $jamPulangSelesai = Carbon::createFromTimeString($pengaturan->jam_pulang_selesai);
            if ($now->isBefore($jamPulangMulai) || $now->isAfter($jamPulangSelesai)) {
                return response()->json(['message' => 'Absen pulang hanya bisa dilakukan antara ' . $jamPulangMulai->format('H:i') . ' - ' . $jamPulangSelesai->format('H:i') . '.'], 400);
            }
            
            $absensi = Absensi::where('karyawan_id', $karyawan->nik)->where('tanggal', $today)->first();
            if (!$absensi || $absensi->jam_pulang) {
                return response()->json(['message' => 'Status absen tidak valid untuk melakukan absen pulang.'], 400);
            }

            $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
            $fileName = 'absen-pulang/' . $karyawan->nik . '-' . $today . '.jpg';
            Storage::disk('public')->put($fileName, $imgData);

            $absensi->update([
                'jam_pulang' => $time,
                'latitude_pulang' => $request->latitude, 'longitude_pulang' => $request->longitude,
                'foto_pulang' => $fileName,
            ]);
            return response()->json(['message' => 'Absen pulang berhasil! Selamat beristirahat.'], 200);
        }
    }
    
    private function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function absenMasuk(Request $request) { return $this->prosesAbsensi($request, 'masuk'); }
    public function absenPulang(Request $request) { return $this->prosesAbsensi($request, 'pulang'); }

    public function riwayat(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        if (empty($karyawan->nik)) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Tidak dapat menampilkan riwayat karena NIK Anda belum terdaftar.');
        }

        // 1. Mulai query builder untuk absensi karyawan ini
        $query = Absensi::where('karyawan_id', $karyawan->nik);

        // 2. Terapkan filter tanggal jika ada input
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            // Validasi untuk memastikan format tanggal benar
            $request->validate([
                'tanggal_mulai' => 'date',
                'tanggal_selesai' => 'date|after_or_equal:tanggal_mulai',
            ]);
            // Terapkan filter whereBetween
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        // 3. Ambil data yang sudah difilter dengan paginasi
        $riwayatAbsensi = $query->orderBy('tanggal', 'desc')
                                ->paginate(10)
                                ->appends($request->query()); // <-- Penting agar filter tidak hilang saat ganti halaman

        // 4. Kirim data ke view
        return view('karyawan.riwayat', compact('riwayatAbsensi'));
    }
}
