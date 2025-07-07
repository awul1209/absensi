<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import semua controller yang dibutuhkan
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\PengaturanAbsensiController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController; // PASTIKAN INI ADA
use App\Http\Controllers\Karyawan\ProfilController;
use App\Http\Controllers\Karyawan\AbsensiController;
use App\Http\Controllers\Admin\PengajuanController as AdminPengajuanController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route halaman depan dan autentikasi
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route ini akan mengarahkan user setelah login sesuai rolenya
Route::get('/home', [HomeController::class, 'index'])->name('home');


// --- GRUP ROUTE UNTUK ADMIN ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // Route::resource('karyawan', KaryawanController::class);
    // <<< TAMBAHKAN RUTE INI >>>
    Route::get('karyawan/export', [KaryawanController::class, 'export'])->name('karyawan.export');
    Route::resource('karyawan', KaryawanController::class); // Letakkan resource setelah rute export kustom

    Route::get('pengaturan-absensi', [PengaturanAbsensiController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan-absensi', [PengaturanAbsensiController::class, 'store'])->name('pengaturan.store');
      Route::get('absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/export', [AdminAbsensiController::class, 'export'])->name('absensi.export'); // Rute Export Absensi

         // =========== RUTE BARU UNTUK MANAJEMEN PENGAJUAN IZIN/SAKIT ADMIN ===========
    Route::get('pengajuan', [AdminPengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('pengajuan/{pengajuanSakitIzin}', [AdminPengajuanController::class, 'show'])->name('pengajuan.show');
    Route::post('pengajuan/{pengajuanSakitIzin}/approve', [AdminPengajuanController::class, 'approve'])->name('pengajuan.approve');
    Route::post('pengajuan/{pengajuanSakitIzin}/reject', [AdminPengajuanController::class, 'reject'])->name('pengajuan.reject');
    // ===========================================================================
    });


// --- GRUP ROUTE UNTUK KARYAWAN ---
Route::middleware(['auth'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');
    
    // Route untuk pendaftaran wajah
    Route::get('profil/pendaftaran-wajah', [ProfilController::class, 'pendaftaranWajah'])->name('profil.pendaftaran-wajah');
    Route::post('profil/simpan-wajah', [ProfilController::class, 'simpanWajah'])->name('profil.simpan-wajah');
    
    // Route untuk halaman absensi utama
    Route::get('absen', [AbsensiController::class, 'index'])->name('absen.index');
    
    // Route untuk memproses data absensi (endpoint untuk JavaScript)
    Route::post('absen/masuk', [AbsensiController::class, 'absenMasuk'])->name('absen.masuk');
    Route::post('absen/pulang', [AbsensiController::class, 'absenPulang'])->name('absen.pulang');

    Route::get('riwayat-absensi', [AbsensiController::class, 'riwayat'])->name('riwayat.index');

    // PERBAIKAN: Menambahkan route untuk halaman profil
    Route::get('profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('profil/update-identitas', [ProfilController::class, 'updateIdentitas'])->name('profil.updateIdentitas');
    Route::put('profil/update-password', [ProfilController::class, 'updatePassword'])->name('profil.updatePassword');

     // =========== RUTE BARU UNTUK PENGAJUAN IZIN/SAKIT KARYAWAN ===========
    Route::get('pengajuan/create', [App\Http\Controllers\Karyawan\PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('pengajuan', [App\Http\Controllers\Karyawan\PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('pengajuan/riwayat', [App\Http\Controllers\Karyawan\PengajuanController::class, 'riwayat'])->name('pengajuan.riwayat'); // Untuk melihat riwayat pengajuan
});