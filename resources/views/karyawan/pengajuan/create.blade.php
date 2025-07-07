@extends('layouts.app') {{-- Pastikan ini adalah layout utama karyawan Anda --}}

@section('title', 'Ajukan Izin / Sakit')

@push('styles')
<style>
    .form-group label {
        font-weight: 600;
        margin-bottom: 0.5rem;
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
    .card-header {
        color: #091D70;
        font-weight: bold;
    }
    /* Opsional: Styling untuk membuat pesan validasi lebih rapi */
    .invalid-feedback {
        display: block; /* Agar pesan invalid-feedback selalu terlihat */
    }
</style>
@endpush

@section('content')
{{-- Kontainer Bootstrap untuk membatasi lebar konten dan menengahkan --}}
<div class="row justify-content-center mt-5">
    <div class="col-lg-8 col-md-10"> {{-- Akan mengambil 8 kolom di layar besar, 10 kolom di layar medium --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-file-earmark-medical me-2"></i>Form Pengajuan Izin / Sakit
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('karyawan.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="karyawan_nama">Nama Karyawan</label>
                                <input type="text" id="karyawan_nama" class="form-control" value="{{ $karyawan->nama_lengkap ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="karyawan_nik">NIK Karyawan</label>
                                <input type="text" id="karyawan_nik" class="form-control" value="{{ $karyawan->nik ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-group">
                            <label for="tipe_pengajuan">Tipe Pengajuan <span class="text-danger">*</span></label>
                            <select name="tipe_pengajuan" id="tipe_pengajuan" class="form-select @error('tipe_pengajuan') is-invalid @enderror" required>
                                <option value="">Pilih Tipe</option>
                                <option value="sakit" {{ old('tipe_pengajuan') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="izin" {{ old('tipe_pengajuan') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="cuti" {{ old('tipe_pengajuan') == 'cuti' ? 'selected' : '' }}>Cuti (Jika ada sistem cuti terpisah)</option>
                            </select>
                            @error('tipe_pengajuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="tanggal_akhir">Tanggal Akhir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control @error('tanggal_akhir') is-invalid @enderror" value="{{ old('tanggal_akhir') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                @error('tanggal_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-group">
                            <label for="alasan">Alasan / Keterangan <span class="text-danger">*</span></label>
                            <textarea name="alasan" id="alasan" rows="4" class="form-control @error('alasan') is-invalid @enderror" placeholder="Jelaskan alasan pengajuan Anda..." required>{{ old('alasan') }}</textarea>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-group">
                            <label for="dokumen_bukti">Dokumen Pendukung (Surat Dokter/Dll.)</label>
                            <input type="file" name="dokumen_bukti" id="dokumen_bukti" class="form-control @error('dokumen_bukti') is-invalid @enderror">
                            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maks: 2MB. (Wajib untuk Sakit)</small>
                            @error('dokumen_bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('karyawan.dashboard') }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-custom-primary">Ajukan Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script> {{-- Pastikan jQuery dimuat --}}
<script>
    $(document).ready(function() {
        // Logika untuk menampilkan pesan wajib upload jika tipe_pengajuan adalah 'sakit'
        $('#tipe_pengajuan').change(function() {
            if ($(this).val() === 'sakit') {
                $('#dokumen_bukti').attr('required', true);
                // Tambahkan tanda bintang (*) jika belum ada
                if ($('label[for="dokumen_bukti"] span.text-danger').length === 0) {
                    $('label[for="dokumen_bukti"]').append(' <span class="text-danger">*</span>');
                }
                $('small.form-text').text('Format: PDF, JPG, JPEG, PNG. Maks: 2MB. (Wajib untuk Sakit)');
            } else {
                $('#dokumen_bukti').attr('required', false);
                // Hapus tanda bintang (*)
                $('label[for="dokumen_bukti"] span.text-danger').remove();
                $('small.form-text').text('Format: PDF, JPG, JPEG, PNG. Maks: 2MB.');
            }
        }).trigger('change'); // Panggil saat halaman dimuat
    });
</script>
@endpush