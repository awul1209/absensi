@extends('layouts.admin-layout') {{-- Pastikan ini layout admin Anda --}}

@section('title', 'Detail Karyawan: ' . $karyawan->nama_lengkap)

@push('styles')
<style>
    .profile-info-item {
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    .profile-info-item strong {
        display: inline-block;
        width: 150px; /* Sesuaikan lebar tetap untuk label */
        font-weight: 600;
        color: #555;
    }
    .profile-picture-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 1.5rem auto;
        border: 3px solid #091D70;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .profile-picture-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-custom-primary {
        background-color: #091D70;
        border-color: #091D70;
        color: white;
    }
    .btn-custom-primary:hover {
        background-color: #07175a;
        border-color: #07175a;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold mb-0">Detail Karyawan</h2>
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="profile-picture-container">
                        @if($karyawan->foto_wajah)
                            <img src="{{ asset('storage/' . $karyawan->foto_wajah) }}" alt="Foto {{ $karyawan->nama_lengkap }}">
                        @else
                            <img src="https://placehold.co/150x150/e9ecef/6c757d?text={{ substr($karyawan->nama_lengkap, 0, 1) }}" alt="Tidak ada foto">
                        @endif
                    </div>
                    <a href="{{ route('admin.karyawan.edit', $karyawan->id) }}" class="btn btn-custom-primary btn-sm mt-2">
                        <i class="bi bi-pencil-square"></i> Edit Karyawan
                    </a>
                </div>
                <div class="col-md-8">
                    <h5 class="fw-bold mb-3" style="color: #091D70;">Informasi Akun & Pribadi</h5>
                    <div class="profile-info-item"><strong>Nama Lengkap:</strong> {{ $karyawan->nama_lengkap }}</div>
                    <div class="profile-info-item"><strong>Email:</strong> {{ $karyawan->user->email ?? '-' }}</div>
                    <div class="profile-info-item"><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
                    <div class="profile-info-item"><strong>Posisi:</strong> {{ $karyawan->posisi }}</div>
                    <div class="profile-info-item"><strong>Jenis Kelamin:</strong> {{ $karyawan->jenis_kelamin ?? '-' }}</div>
                    <div class="profile-info-item"><strong>Nomor Telepon:</strong> {{ $karyawan->nomor_telepon ?? '-' }}</div>
                    <div class="profile-info-item"><strong>Tahun Masuk:</strong> {{ $karyawan->tahun_masuk ?? '-' }}</div>
                    <div class="profile-info-item"><strong>Alamat:</strong> {{ $karyawan->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection