<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon; // Tambahkan ini jika belum ada

use Maatwebsite\Excel\Facades\Excel; // Untuk ekspor Excel
use App\Exports\KaryawanExport; // Kelas ekspor kustom Anda
use Barryvdh\DomPDF\Facade\Pdf; // Untuk ekspor PDF

class KaryawanController extends Controller
{
    /**
     * Menampilkan daftar semua karyawan (Read).
     */
    public function index(Request $request)
    {
        $query = Karyawan::with('user')->orderBy('nama_lengkap', 'asc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('posisi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tahun_masuk')) {
            $query->where('tahun_masuk', $request->input('tahun_masuk'));
        }

        $karyawans = $query->paginate(10)->withQueryString();

        $tahunMasukOptions = Karyawan::select('tahun_masuk')
                                    ->distinct()
                                    ->whereNotNull('tahun_masuk')
                                    ->orderBy('tahun_masuk', 'desc')
                                    ->pluck('tahun_masuk');

        return view('admin.karyawan.index', compact('karyawans', 'tahunMasukOptions'));
    }

    /**
     * Menampilkan form untuk membuat karyawan baru (Create).
     */
    public function create()
    {
        return view('admin.karyawan.create');
    }

    /**
     * Menyimpan data karyawan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'posisi' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255|unique:karyawans,nik',
            'nomor_telepon' => 'nullable|string|max:15',
            'tahun_masuk' => 'nullable|digits:4|integer|min:1990|max:' . (date('Y') + 1),
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'foto_wajah' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'karyawan',
        ]);

        $karyawanData = $request->only([
            'nama_lengkap', 'posisi', 'nik', 'tahun_masuk', 'jenis_kelamin', 'alamat', 'nomor_telepon'
        ]);
        $karyawanData['user_id'] = $user->id;

        if ($request->hasFile('foto_wajah')) {
            $path = $request->file('foto_wajah')->store('foto_wajah', 'public');
            $karyawanData['foto_wajah'] = $path;
        }

        Karyawan::create($karyawanData);

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail karyawan (Show). <<< BARIS INI YANG DITAMBAHKAN
     */
    public function show(Karyawan $karyawan)
    {
        $karyawan->load('user'); // Memuat relasi user jika digunakan di view
        return view('admin.karyawan.show', compact('karyawan'));
    }

    /**
     * Menampilkan form untuk mengedit data karyawan (Update).
     */
    public function edit(Karyawan $karyawan)
    {
        $karyawan->load('user');
        return view('admin.karyawan.edit', compact('karyawan'));
    }

    /**
     * Memperbarui data karyawan di database.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($karyawan->user_id)],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
            'posisi' => 'required|string|max:255',
            'nik' => ['nullable', 'string', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)],
            'nomor_telepon' => 'nullable|string|max:15',
            'tahun_masuk' => 'nullable|digits:4|integer|min:1990|max:' . (date('Y') + 1),
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'foto_wajah' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $karyawan->user->update([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
        ]);
        if ($request->filled('password')) {
            $karyawan->user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        $karyawanData = $request->only([
            'nama_lengkap', 'posisi', 'nik', 'tahun_masuk', 'jenis_kelamin', 'alamat', 'nomor_telepon'
        ]);

        if ($request->hasFile('foto_wajah')) {
            if ($karyawan->foto_wajah && Storage::disk('public')->exists($karyawan->foto_wajah)) {
                Storage::disk('public')->delete($karyawan->foto_wajah);
            }
            $path = $request->file('foto_wajah')->store('foto_wajah', 'public');
            $karyawanData['foto_wajah'] = $path;
        } else {
            $karyawanData['foto_wajah'] = $karyawan->foto_wajah;
        }

        $karyawan->update($karyawanData);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Menghapus data karyawan (Delete).
     */
    public function destroy(Karyawan $karyawan)
    {
        if ($karyawan->foto_wajah) {
            Storage::disk('public')->delete($karyawan->foto_wajah);
        }
        if ($karyawan->foto_referensi) {
            Storage::disk('public')->delete($karyawan->foto_referensi);
        }

        $karyawan->user->delete();
        
        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    /**
     * Export data karyawan ke PDF atau Excel.
     */
public function export(Request $request)
{
    $type = $request->query('type');
    $tahunMasuk = $request->query('tahun_masuk');

    if ($type === 'excel') {
        $fileName = 'data_karyawan_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new KaryawanExport($tahunMasuk), $fileName);
    } elseif ($type === 'pdf') {
        $query = Karyawan::query();
        if ($tahunMasuk) {
            $query->where('tahun_masuk', $tahunMasuk);
        }
        $karyawans = $query->with('user')->get();

        $pdf = Pdf::loadView('admin.karyawan.pdf_report', compact('karyawans', 'tahunMasuk'));
        $fileName = 'data_karyawan_' . Carbon::now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    return redirect()->back()->with('error', 'Tipe ekspor tidak valid.');
}
}