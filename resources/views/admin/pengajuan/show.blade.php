@extends('admin.layouts.app')

@section('title', 'Tinjau Pengajuan Absen')

@push('styles')
<style>
    .card-header {
        border-bottom: 1px solid #dee2e6;
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
    .detail-item strong {
        display: block;
        margin-bottom: 0.25rem;
        color: #555;
    }
    .badge-pending { background-color: #ffc107; color: #333; }
    .badge-approved { background-color: #28a745; color: white; }
    .badge-rejected { background-color: #dc3545; color: white; }
    .document-preview {
        max-width: 100%;
        height: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-top: 10px;
    }
    .pdf-viewer {
        width: 100%;
        height: 500px; /* Atur tinggi iframe PDF sesuai kebutuhan */
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-top: 10px;
    }
    .form-group label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .invalid-feedback {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold" style="color: #091D70;">
                <i class="bi bi-file-earmark-medical me-2"></i>Tinjau Pengajuan Absen
            </h5>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '{{ session('error') }}',
                    });
                </script>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="detail-item">
                        <strong>Nama Karyawan:</strong>
                        <p>{{ $pengajuanSakitIzin->karyawan->nama_lengkap ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="detail-item">
                        <strong>NIK Karyawan:</strong>
                        <p>{{ $pengajuanSakitIzin->karyawan_id_nik }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="detail-item">
                        <strong>Tipe Pengajuan:</strong>
                        <p>{{ ucfirst($pengajuanSakitIzin->tipe_pengajuan) }}</p>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="detail-item">
                        <strong>Periode:</strong>
                        <p>
                            {{ \Carbon\Carbon::parse($pengajuanSakitIzin->tanggal_mulai)->translatedFormat('d M Y') }}
                            @if($pengajuanSakitIzin->tanggal_mulai != $pengajuanSakitIzin->tanggal_akhir)
                                - {{ \Carbon\Carbon::parse($pengajuanSakitIzin->tanggal_akhir)->translatedFormat('d M Y') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="detail-item">
                    <strong>Alasan / Keterangan:</strong>
                    <p>{{ $pengajuanSakitIzin->alasan }}</p>
                </div>
            </div>

            <div class="mb-3">
                <div class="detail-item">
                    <strong>Status Pengajuan:</strong>
                    @php
                        $badgeClass = '';
                        switch ($pengajuanSakitIzin->status_persetujuan) {
                            case 'pending': $badgeClass = 'badge-pending'; break;
                            case 'approved': $badgeClass = 'badge-approved'; break;
                            case 'rejected': $badgeClass = 'badge-rejected'; break;
                            default: $badgeClass = 'badge-secondary'; break;
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($pengajuanSakitIzin->status_persetujuan) }}</span>
                </div>
            </div>

            @if($pengajuanSakitIzin->disetujui_oleh_admin_id)
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="detail-item">
                        <strong>Persetujuan Oleh:</strong>
                        <p>{{ $pengajuanSakitIzin->admin->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="detail-item">
                        <strong>Tanggal Persetujuan:</strong>
                        <p>{{ \Carbon\Carbon::parse($pengajuanSakitIzin->tanggal_persetujuan)->translatedFormat('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if($pengajuanSakitIzin->status_persetujuan == 'rejected')
            <div class="mb-3">
                <div class="detail-item">
                    <strong>Alasan Penolakan:</strong>
                    <p class="text-danger">{{ $pengajuanSakitIzin->alasan_penolakan ?? '-' }}</p>
                </div>
            </div>
            @endif

            <div class="mb-3">
                <div class="detail-item">
                    <strong>Dokumen Pendukung:</strong>
                    @if($pengajuanSakitIzin->dokumen_bukti_path)
                        <p>
                            <a href="{{ asset('storage/' . $pengajuanSakitIzin->dokumen_bukti_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Dokumen
                            </a>
                        </p>
                        @php
                            $fileExtension = pathinfo($pengajuanSakitIzin->dokumen_bukti_path, PATHINFO_EXTENSION);
                        @endphp
                        @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset('storage/' . $pengajuanSakitIzin->dokumen_bukti_path) }}" alt="Dokumen Bukti" class="document-preview">
                        @elseif($fileExtension == 'pdf')
                            <iframe src="{{ asset('storage/' . $pengajuanSakitIzin->dokumen_bukti_path) }}" class="pdf-viewer"></iframe>
                        @endif
                    @else
                        <p>-</p>
                    @endif
                </div>
            </div>

            @if($pengajuanSakitIzin->status_persetujuan == 'pending')
                <hr>
                <h6 class="fw-bold mb-3">Aksi Admin:</h6>
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.pengajuan.approve', $pengajuanSakitIzin->id) }}" method="POST" class="d-inline approve-form">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Setujui</button>
                    </form>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pengajuan.reject', $pengajuanSakitIzin->id) }}" method="POST" class="reject-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="alasan_penolakan">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_penolakan" id="alasan_penolakan" class="form-control @error('alasan_penolakan') is-invalid @enderror" rows="4" required>{{ old('alasan_penolakan') }}</textarea>
                        @error('alasan_penolakan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // SweetAlert untuk konfirmasi persetujuan
            $('.approve-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pengajuan ini akan disetujui dan status absensi akan diperbarui.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // SweetAlert untuk konfirmasi penolakan (triggered by modal submit)
            $('.reject-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Tolak Pengajuan?',
                    text: "Pengajuan ini akan ditolak dan statusnya diperbarui.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Tolak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Tampilkan modal tolak jika ada error validasi saat submit form tolak
            @if ($errors->has('alasan_penolakan'))
                var rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                rejectModal.show();
            @endif
        });
    </script>
@endpush