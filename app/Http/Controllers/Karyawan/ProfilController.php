<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;


class ProfilController extends Controller
{
    /**
     * Menampilkan halaman edit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        return view('karyawan.profil.edit', compact('user', 'karyawan'));
    }

    /**
     * Memperbarui data identitas karyawan, termasuk foto profil.
     */
     public function updateIdentitas(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        // 1. Validasi input, termasuk file foto
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => ['required', 'string', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)],
            'nomor_telepon' => 'nullable|string|max:15',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'foto_wajah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048' // Validasi untuk foto
        ]);

        // 2. Siapkan data untuk diupdate
        $updateData = $request->only([
            'nama_lengkap', 'nik', 'nomor_telepon', 'jenis_kelamin', 'alamat'
        ]);

        // 3. Logika untuk menangani unggahan foto baru
        if ($request->hasFile('foto_wajah')) {
            // Hapus foto lama dari storage jika ada dan file-nya eksis
            if ($karyawan->foto_wajah && Storage::disk('public')->exists($karyawan->foto_wajah)) {
                Storage::disk('public')->delete($karyawan->foto_wajah);
            }

            // Simpan foto baru ke folder 'foto_wajah' di public disk
            // Ini akan mengembalikan path seperti 'foto_wajah/namafileunik.jpg'
            $path = $request->file('foto_wajah')->store('foto_wajah', 'public'); 
            
            // Simpan path yang bersih langsung ke array data
            $updateData['foto_wajah'] = $path; // Tidak perlu str_replace lagi
        }

        // 4. Update data di tabel users dan karyawans
        $user->update(['name' => $request->nama_lengkap]);
        $karyawan->update($updateData);

        return back()->with('status', 'identitas-updated');
    }

    /**
     * Memperbarui password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated');
    }

    // ... (method pendaftaranWajah dan simpanWajah tidak berubah) ...
    public function pendaftaranWajah()
    {
        return view('karyawan.profil.pendaftaran-wajah');
    }

    public function simpanWajah(Request $request)
    {
        $request->validate(['image' => 'required']);
        $karyawan = Auth::user()->karyawan;
        if (!$karyawan) {
            return response()->json(['message' => 'Profil karyawan tidak ditemukan.'], 404);
        }
        if (empty($karyawan->nik)) {
            return response()->json(['message' => 'NIK Anda belum terdaftar. Harap hubungi Admin untuk melengkapi data.'], 400);
        }
        $fileName = 'referensi/' . $karyawan->nik . '.jpg';
        if ($karyawan->foto_referensi && Storage::disk('public')->exists($karyawan->foto_referensi)) {
            Storage::disk('public')->delete($karyawan->foto_referensi);
        }
        $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
        Storage::disk('public')->put($fileName, $imgData);
        $karyawan->update(['foto_referensi' => $fileName]);
        return response()->json(['message' => 'Wajah berhasil diperbarui!'], 200);
    }
}
