@extends('admin.layouts.app') {{-- Pastikan ini mengarah ke layout admin Anda --}}

@section('title', 'Ubah Data Karyawan')

@push('styles')
<style>
    /* Tambahkan styling khusus jika diperlukan, atau ambil dari layout admin */
    .profile-picture-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ccc;
        background-color: #f8f9fa;
        display: block;
        margin-bottom: 10px;
    }
    .form-label {
        font-weight: 500;
        font-size: 14px;
    }
    .form-control, .form-select, .form-text {
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h2 class="font-weight-bold mb-4">Ubah Data Karyawan</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            {{-- Pesan error validasi akan ditangani oleh SweetAlert di layout utama --}}

            <form method="POST" action="{{ route('admin.karyawan.update', $karyawan->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h5 class="mt-2">Informasi Akun</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
                        @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $karyawan->user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password Baru (Opsional)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h5 class="mt-4">Informasi Pribadi & Pekerjaan</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $karyawan->nik) }}">
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="posisi" class="form-label">Posisi</label>
                        <input type="text" class="form-control @error('posisi') is-invalid @enderror" id="posisi" name="posisi" value="{{ old('posisi', $karyawan->posisi) }}" required>
                        @error('posisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nomor_telepon" class="form-label">Nomor Telepon (Opsional)</label>
                        <input type="text" class="form-control @error('nomor_telepon') is-invalid @enderror" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}">
                        @error('nomor_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tahun_masuk" class="form-label">Tahun Masuk (Opsional)</label>
                        <input type="number" class="form-control @error('tahun_masuk') is-invalid @enderror" id="tahun_masuk" name="tahun_masuk" value="{{ old('tahun_masuk', $karyawan->tahun_masuk) }}" placeholder="Contoh: 2024">
                        @error('tahun_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="alamat" class="form-label">Alamat (Opsional)</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $karyawan->alamat) }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h5 class="mt-4">Foto Profil (Opsional)</h5>
                <hr>
                <div class="mb-3">
                    <label for="foto_wajah" class="form-label">Ubah Foto Profil</label>
                    <img id="fotoPreview" src="{{ $karyawan->foto_wajah ? asset('storage/' . $karyawan->foto_wajah) : 'https://placehold.co/100x100/e9ecef/6c757d?text=Foto' }}" alt="Foto Profil" class="profile-picture-preview">
                    <input class="form-control @error('foto_wajah') is-invalid @enderror" type="file" id="foto_wajah" name="foto_wajah" accept="image/png, image/jpeg, image/jpg">
                    <div class="form-text">Kosongkan jika tidak ingin mengubah foto.</div>
                    @error('foto_wajah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fotoInput = document.getElementById('foto_wajah');
        const fotoPreview = document.getElementById('fotoPreview');

        if (fotoInput && fotoPreview) {
            fotoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        fotoPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Jika tidak ada file baru dipilih, pertahankan foto lama atau gunakan placeholder
                    fotoPreview.src = "{{ $karyawan->foto_wajah ? asset('storage/' . $karyawan->foto_wajah) : 'https://placehold.co/100x100/e9ecef/6c757d?text=Foto' }}";
                }
            });
        }
    });
</script>
@endpush