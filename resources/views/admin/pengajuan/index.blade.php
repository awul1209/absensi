@extends('admin.layouts.app') {{-- Pastikan ini mengarah ke layout admin Anda --}}

@section('title', 'Manajemen Pengajuan Absen')

@push('styles')
<style>
    .card-header {
        border-bottom: 1px solid #dee2e6;
    }
    #pengajuanTable th, #pengajuanTable td {
        font-size: 13px;
        vertical-align: middle;
        padding: 0.5rem 0.75rem;
    }
    #pengajuanTable th {
        font-weight: 600;
        white-space: nowrap;
    }
    /* Warna badge status */
    .badge-pending { background-color: #ffc107; color: #333; }
    .badge-approved { background-color: #28a745; color: white; }
    .badge-rejected { background-color: #dc3545; color: white; }
    .btn-action {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
    .btn-custom-primary { /* Definisi warna tombol untuk Admin */
        background-color: #091D70;
        border-color: #091D70;
        color: white;
    }
    .btn-custom-primary:hover {
        background-color: #07175a;
        border-color: #07175a;
        color: white;
    }
    /* Filter styling */
    .form-control-sm, .form-select-sm {
        height: calc(1.8125rem + 2px);
        padding: .25rem .5rem;
        font-size: .875rem;
    }
    @media (max-width: 767.98px) {
        .card-header .d-flex.flex-wrap {
            flex-direction: column;
            align-items: stretch !important;
            gap: 0.5rem;
        }
        .card-header .d-flex.flex-wrap > *,
        .card-header .d-flex.flex-wrap .form-select,
        .card-header .d-flex.flex-wrap .form-control {
            width: 100% !important;
            margin-bottom: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
            <h5 class="mb-3 mb-md-0 fw-bold" style="color: #091D70;">
                <i class="bi bi-file-earmark-medical me-2"></i>Manajemen Pengajuan Absen
            </h5>
            
            <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
                {{-- Filter Form --}}
                <form action="{{ route('admin.pengajuan.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="status" id="status_filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    <select name="tipe" id="tipe_filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="all" {{ $tipeFilter == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="sakit" {{ $tipeFilter == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ $tipeFilter == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="cuti" {{ $tipeFilter == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    </select>

                    <input type="date" name="start_date" class="form-control form-control-sm" placeholder="Dari Tanggal" value="{{ $startDate }}" style="width: auto;">
                    <input type="date" name="end_date" class="form-control form-control-sm" placeholder="Sampai Tanggal" value="{{ $endDate }}" style="width: auto;">
                    
                    <button type="submit" class="btn btn-custom-primary btn-sm">Filter</button>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </form>
            </div>
        </div>
        <div class="card-body">
            {{-- Pesan SweetAlert: Tidak lagi ada blok <script> di sini. Akan ditangani di @push('scripts') --}}
            
            <div class="table-responsive">
                <table id="pengajuanTable" class="table table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Nama Karyawan</th>
                            <th>Tipe</th>
                            <th>Periode</th>
                            <th>Alasan</th>
                            <th>Dokumen</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuans as $index => $pengajuan)
                        <tr>
                            <td class="text-center">{{ $index + $pengajuans->firstItem() }}</td>
                            <td>{{ \Carbon\Carbon::parse($pengajuan->created_at)->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $pengajuan->karyawan->nama_lengkap ?? '-' }}</td>
                            <td>{{ ucfirst($pengajuan->tipe_pengajuan) }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d M Y') }} 
                                @if($pengajuan->tanggal_mulai != $pengajuan->tanggal_akhir)
                                    - {{ \Carbon\Carbon::parse($pengajuan->tanggal_akhir)->translatedFormat('d M Y') }}
                                @endif
                            </td>
                            <td>{{ Str::limit($pengajuan->alasan, 50, '...') }}</td>
                            <td>
                                @if($pengajuan->dokumen_bukti_path)
                                    <a href="{{ asset('storage/' . $pengajuan->dokumen_bukti_path) }}" target="_blank" class="text-decoration-none" title="Lihat Dokumen">
                                        <i class="bi bi-file-earmark-text-fill"></i> Lihat
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = '';
                                    switch ($pengajuan->status_persetujuan) {
                                        case 'pending': $badgeClass = 'badge-pending'; break;
                                        case 'approved': $badgeClass = 'badge-approved'; break;
                                        case 'rejected': $badgeClass = 'badge-rejected'; break;
                                        default: $badgeClass = 'badge-secondary'; break;
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($pengajuan->status_persetujuan) }}</span>
                            </td>
                            <td class="text-center">
                                @if($pengajuan->status_persetujuan == 'pending')
                                    <a href="{{ route('admin.pengajuan.show', $pengajuan->id) }}" class="btn btn-info btn-action" title="Tinjau">
                                        <i class="bi bi-eye"></i> Tinjau
                                    </a>
                                @else
                                    <a href="{{ route('admin.pengajuan.show', $pengajuan->id) }}" class="btn btn-secondary btn-action" title="Lihat Detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data pengajuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pengajuans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Memuat jQuery dan SweetAlert2 --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Logika SweetAlert untuk pesan sukses dari session
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                });
            @endif

            // Logika SweetAlert untuk pesan error dari session
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                });
            @endif

            // Anda bisa menambahkan logika DataTables di sini jika diperlukan
            /*
            $('#pengajuanTable').DataTable({
                "language": {
                    "url": "/js/datatables/id.json"
                },
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "columnDefs": [
                    { "orderable": false, "targets": [0, 5, 8] } // Non-sortable columns
                ]
            });
            */
        });
    </script>
@endpush