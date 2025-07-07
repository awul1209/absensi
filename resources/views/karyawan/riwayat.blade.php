@extends('layouts.app')

@section('title', 'Riwayat Absensi Saya')

@push('styles')
<style>
    .filter-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .table thead th {
        background-color: #e9ecef;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        white-space: nowrap;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .img-thumbnail-custom {
        width: 60px;
        height: 60px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .img-thumbnail-custom:hover {
        transform: scale(1.1);
    }
    @media (max-width: 767px) {
        .table {
            font-size: 14px;
        }
        .table th, .table td {
            padding: 0.5rem;
        }
        .img-thumbnail-custom {
            width: 45px;
            height: 45px;
        }
    }
</style>
@endpush

@section('content')
{{-- PERBAIKAN 1: Mengubah dari container-fluid menjadi container --}}
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold">Riwayat Absensi Saya</h2>
    </div>

    <!-- Form Filter Tanggal -->
    <div class="card shadow-sm border-0 mb-4 filter-card">
        <div class="card-body">
            {{-- PERBAIKAN 2: Mengubah layout form filter --}}
            <form action="{{ route('karyawan.riwayat.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="tanggal_mulai" class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-4">
                    <label for="tanggal_selesai" class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary w-100 me-2"><i class="bi bi-search"></i> Filter</button>
                        <a href="{{ route('karyawan.riwayat.index') }}" class="btn btn-secondary w-100"><i class="bi bi-arrow-clockwise"></i> Semua</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Foto Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Status</th>
                            <th>Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatAbsensi as $index => $absen)
                            <tr>
                                <td>{{ $riwayatAbsensi->firstItem() + $index }}</td>
                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i:s') : '-' }}</td>
                                <td>
                                    @if ($absen->foto_masuk)
                                        <img src="{{ Storage::url($absen->foto_masuk) }}" alt="Foto Masuk" 
                                             class="img-thumbnail-custom rounded-circle"
                                             data-bs-toggle="modal" data-bs-target="#imageModal" 
                                             data-bs-image="{{ Storage::url($absen->foto_masuk) }}"
                                             data-bs-title="Foto Absen Masuk - {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i:s') : '-' }}</td>
                                 <td>
                                    @if ($absen->foto_pulang)
                                        <img src="{{ Storage::url($absen->foto_pulang) }}" alt="Foto Pulang" 
                                             class="img-thumbnail-custom rounded-circle"
                                             data-bs-toggle="modal" data-bs-target="#imageModal" 
                                             data-bs-image="{{ Storage::url($absen->foto_pulang) }}"
                                             data-bs-title="Foto Absen Pulang - {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($absen->status_masuk == 'tepat_waktu')
                                        <span class="badge text-bg-success">Tepat Waktu</span>
                                    @elseif($absen->status_masuk == 'terlambat')
                                        <span class="badge text-bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge text-bg-secondary">{{ $absen->status }}</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $absen->keterlambatan_menit > 0 ? $absen->keterlambatan_menit . ' menit' : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="mb-0">Tidak ada data riwayat absensi yang ditemukan.</p>
                                    @if(request()->has('tanggal_mulai'))
                                        <small class="text-muted">Coba ubah rentang tanggal filter Anda atau klik tombol "Semua".</small>
                                    @endif
                                </td>
                            </tr>
                        @endempty
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $riwayatAbsensi->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Foto Absen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="modalImage" class="img-fluid rounded" alt="Foto Absen">
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', event => {
            const triggerElement = event.relatedTarget;
            const imageUrl = triggerElement.getAttribute('data-bs-image');
            const imageTitle = triggerElement.getAttribute('data-bs-title');
            
            const modalTitle = imageModal.querySelector('.modal-title');
            const modalImage = imageModal.querySelector('#modalImage');

            modalTitle.textContent = imageTitle;
            modalImage.src = imageUrl;
        });
    }
</script>
@endpush
