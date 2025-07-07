{{-- Menggunakan layout admin yang baru kita buat --}}
@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold">Dashboard</h2>
        <div class="text-muted">
            <i class="bi bi-calendar-event-fill me-2"></i> {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Total Karyawan</p>
                        {{-- Ganti dengan data asli dari controller nanti --}}
                        <h4 class="mb-0 fw-bold">15</h4> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                 <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-check-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Hadir Hari Ini</p>
                        <h4 class="mb-0 fw-bold">12</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                 <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-exclamation-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Terlambat Hari Ini</p>
                        <h4 class="mb-0 fw-bold">2</h4>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                 <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-x-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Izin / Sakit</p>
                        <h4 class="mb-0 fw-bold">1</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Akses Cepat</h3>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0 h-100 p-3">
                <a href="{{ route('admin.karyawan.create') }}" class="text-decoration-none text-dark">
                    <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
                    <h5 class="mt-3">Tambah Karyawan Baru</h5>
                    <p class="text-muted">Mendaftarkan karyawan baru ke dalam sistem.</p>
                </a>
            </div>
        </div>
        <div class="col-md-4">
             <div class="card text-center shadow-sm border-0 h-100 p-3">
                <a href="#" class="text-decoration-none text-dark">
                    <i class="bi bi-card-checklist fs-1 text-success"></i>
                    <h5 class="mt-3">Lihat Laporan Absensi</h5>
                    <p class="text-muted">Melihat dan mengunduh rekap absensi karyawan.</p>
                </a>
            </div>
        </div>
        <div class="col-md-4">
             <div class="card text-center shadow-sm border-0 h-100 p-3">
                <a href="{{ route('admin.pengaturan.index') }}" class="text-decoration-none text-dark">
                    <i class="bi bi-sliders fs-1 text-warning"></i>
                    <h5 class="mt-3">Atur Jam & Lokasi</h5>
                    <p class="text-muted">Mengubah konfigurasi jam kerja dan lokasi absensi.</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
