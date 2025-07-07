@extends('layouts.app') {{-- Berdasarkan konfirmasi Anda, ini adalah layout yang benar --}}
@section('title', 'Riwayat Pengajuan Saya')

@push('styles')
<style>
    .card-header {
        border-bottom: 1px solid #dee2e6;
    }
    #pengajuanTable th, #pengajuanTable td {
        font-size: 13px;
        vertical-align: middle;
    }
    #pengajuanTable th {
        font-weight: 600;
    }
    .badge-pending { background-color: #ffc107; color: #333; }
    .badge-approved { background-color: #28a745; color: white; }
    .badge-rejected { background-color: #dc3545; color: white; }
    .btn-view-document {
        color: #091D70;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
{{-- Kontainer Bootstrap untuk membatasi lebar konten dan menengahkan --}}
<div class="row justify-content-center mt-5">
    <div class="col-lg-10 col-md-12"> {{-- Sedikit lebih lebar karena ini tabel, sesuaikan jika perlu --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold" style="color: #091D70;">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Pengajuan Saya
                </h5>
                <a href="{{ route('karyawan.pengajuan.create') }}" class="btn btn-custom-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Ajukan Baru
                </a>
            </div>
            <div class="card-body">
                {{-- Pesan SweetAlert: TIDAK ADA LAGI BLOK <script> di sini. Akan ditangani di @push('scripts') --}}

                <div class="table-responsive">
                    <table id="pengajuanTable" class="table table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tipe</th>
                                <th>Periode</th>
                                <th>Alasan</th>
                                <th>Dokumen</th>
                                <th>Status</th>
                                <th>Persetujuan Oleh</th>
                                <th>Alasan Penolakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengajuans as $index => $pengajuan)
                            <tr>
                                <td class="text-center">{{ $index + $pengajuans->firstItem() }}</td>
                                <td>{{ \Carbon\Carbon::parse($pengajuan->created_at)->translatedFormat('d M Y H:i') }}</td>
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
                                        <a href="{{ asset('storage/' . $pengajuan->dokumen_bukti_path) }}" target="_blank" class="btn-view-document" title="Lihat Dokumen">
                                            <i class="bi bi-file-earmark-text-fill"></i>
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
                                <td>{{ $pengajuan->admin->name ?? '-' }}</td>
                                <td>{{ $pengajuan->alasan_penolakan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Tidak ada riwayat pengajuan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $pengajuans->links() }}
                </div>
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