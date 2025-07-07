@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="font-weight-bold mb-4">Profil Saya</h2>

            {{-- Kartu untuk Update Informasi Pribadi --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-fill me-2"></i>Informasi Pribadi</h5>
                </div>
                <div class="card-body p-4">
                    @if (session('status') === 'identitas-updated')
                        <div class="alert alert-success" role="alert">
                            Informasi pribadi berhasil diperbarui.
                        </div>
                    @endif

                    {{-- Menambahkan enctype untuk form unggah file --}}
                    <form method="post" action="{{ route('karyawan.profil.updateIdentitas') }}" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        {{-- Bagian untuk menampilkan foto dan input unggah --}}
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <img id="photoPreview" 
                                     src="{{ $karyawan->foto_wajah ? Storage::url($karyawan->foto_wajah) : 'https://placehold.co/150x150/EFEFEF/AAAAAA&text=Foto' }}" 
                                     alt="Foto Profil" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            <div class="col-md-9 d-flex flex-column justify-content-center">
                                <label for="foto_wajah" class="form-label">Ubah Foto Profil</label>
                                <input class="form-control @error('foto_wajah') is-invalid @enderror" type="file" id="foto_wajah" name="foto_wajah" accept="image/png, image/jpeg, image/jpg">
                                @error('foto_wajah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Pilih file gambar (JPG, PNG) dengan ukuran maksimal 2MB.</div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input id="nama_lengkap" name="nama_lengkap" type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
                                @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" type="email" class="form-control" value="{{ $user->email }}" disabled readonly>
                                <div class="form-text">Email tidak dapat diubah.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input id="nik" name="nik" type="text" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $karyawan->nik) }}" required>
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                <input id="nomor_telepon" name="nomor_telepon" type="text" class="form-control @error('nomor_telepon') is-invalid @enderror" value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}">
                                @error('nomor_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="Laki-laki" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $karyawan->alamat) }}</textarea>
                                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan Identitas</button>
                    </form>
                </div>
            </div>

            {{-- Kartu untuk Ubah Password --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-key-fill me-2"></i>Ubah Password</h5>
                </div>
                <div class="card-body p-4">
                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success" role="alert">
                            Password berhasil diperbarui.
                        </div>
                    @endif

                    <form method="post" action="{{ route('karyawan.profil.updatePassword') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>
                            @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>
                             @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Ubah Password</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.getElementById('foto_wajah');
        const photoPreview = document.getElementById('photoPreview');

        if (photoInput && photoPreview) {
            photoInput.addEventListener('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    photoPreview.src = URL.createObjectURL(file);
                }
            });
        }
    });
</script>
@endpush
