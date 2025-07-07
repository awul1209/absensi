@extends('layouts.app')

@section('content')
<div class="container mt-5">
    {{-- Menampilkan pesan dari controller --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h2>
                    <p class="card-text text-muted">Siap untuk produktif hari ini? Lakukan absensi atau lihat riwayat Anda melalui menu di bawah.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Tombol Absen Wajah --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-center">
                    {{-- PASTIKAN BARIS INI SUDAH BENAR --}}
                    <a href="{{ route('karyawan.absen.index') }}" class="text-decoration-none text-dark">
                        <i class="bi bi-camera-video-fill display-1 text-primary"></i>
                        <h4 class="mt-3">Absen Wajah</h4>
                        <p class="text-muted">Klik di sini untuk melakukan absensi masuk atau pulang.</p>
                    </a>
                </div>
            </div>
        </div>

        {{-- Tombol Daftar Wajah --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-center">
                    <a href="{{ route('karyawan.profil.pendaftaran-wajah') }}" class="text-decoration-none text-dark">
                        <i class="bi bi-person-vcard display-1 text-info"></i>
                        <h4 class="mt-3">Daftarkan Wajah</h4>
                        <p class="text-muted">Daftarkan atau perbarui foto wajah Anda untuk absensi.</p>
                    </a>
                </div>
            </div>
        </div>

        {{-- Tombol Riwayat Absen --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-center">
                    <a href="{{ route('karyawan.riwayat.index') }}" class="text-decoration-none text-dark"> {{-- Ganti # dengan route riwayat nanti --}}
                        <i class="bi bi-calendar-range-fill display-1 text-success"></i>
                        <h4 class="mt-3">Riwayat Absensi</h4>
                        <p class="text-muted">Lihat rekap kehadiran dan jam kerja Anda.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
