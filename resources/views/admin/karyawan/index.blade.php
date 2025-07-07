@extends('admin.layouts.app') {{-- Pastikan ini mengarah ke layout admin Anda --}}

@section('title', 'Manajemen Karyawan')

@push('styles')
<style>
    /* Styling umum dari layout admin Anda */
    .card-header {
        border-bottom: 1px solid #dee2e6;
    }
    #karyawanTable th, #karyawanTable td {
        font-size: 13px; /* Font agak kecil */
        vertical-align: middle;
    }
    #karyawanTable th {
        font-weight: 600;
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
    .btn-group .btn {
        transition: background-color 0.2s ease-in-out;
        color: #495057; /* Warna default icon */
        font-size: 18px; /* Ukuran icon */
    }
    .btn-group .btn:hover {
        background-color: rgba(0, 0, 0, 0.07);
    }
    /* Warna spesifik untuk ikon aksi */
    .btn-group .btn .bi-pencil-square { color: #091D70; }
    .btn-group .btn .bi-trash-fill { color: red; }

    /* Responsifitas header filter/export */
    @media (max-width: 767.98px) {
        .card-header .d-flex.flex-wrap {
            flex-direction: column;
            align-items: stretch !important; /* Regangkan item */
        }
        .card-header .d-flex.flex-wrap > *,
        .card-header .d-flex.flex-wrap .form-select {
            width: 100% !important; /* Ambil lebar penuh */
            margin-bottom: 0.5rem; /* Jarak antar item */
        }
        .card-header .d-flex.flex-wrap > *:last-child {
            margin-bottom: 0; /* Hapus margin bawah di item terakhir */
        }
        .card-header .d-flex.flex-wrap .btn-group {
            flex-direction: row; /* Pastikan tombol export tetap sebaris */
        }
        .card-header .d-flex.flex-wrap .btn-group .btn {
            flex-grow: 1; /* Biarkan tombol export memenuhi */
        }
    }
</style>
@endpush

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
        <h5 class="mb-3 mb-md-0 fw-bold" style="color: #091D70;">
            <i class="bi bi-person-badge me-2"></i>Manajemen Karyawan
        </h5>
        
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            {{-- Form Filter Tahun Masuk --}}
            <form action="{{ route('admin.karyawan.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <select name="tahun_masuk" id="tahun_masuk_filter" class="form-select form-select-sm">
                    <option value="">Filter Tahun Masuk</option>
                    @foreach ($tahunMasukOptions as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun_masuk') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-custom-primary btn-sm">Filter</button>
                <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </form>

            {{-- Tombol Export --}}
            <div class="btn-group">
                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" id="exportExcelBtn">Export ke Excel</a></li>
                    <li><a class="dropdown-item" href="#" id="exportPdfBtn">Export ke PDF</a></li>
                </ul>
            </div>

            {{-- Tombol Tambah Karyawan --}}
            <a href="{{ route('admin.karyawan.create') }}" class="btn btn-custom-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Tambah Karyawan
            </a>
        </div>
    </div>
    <div class="card-body">
        {{-- Pesan SweetAlert akan ditangani oleh layout utama --}}
        <div class="table-responsive">
            <table id="karyawanTable" class="table table-hover" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Foto</th>
                        <th>Nama Lengkap</th>
                        <th>NIK</th>
                        <th>Posisi</th>
                        <th>Email</th>
                        <th>Tahun Masuk</th> {{-- Kolom Tahun Masuk --}}
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($karyawans as $karyawan)
                    <tr>
                        <td class="text-center"></td>
                        <td>
                            @if($karyawan->foto_wajah)
                                <img src="{{ asset('storage/' . $karyawan->foto_wajah) }}" alt="Foto {{ $karyawan->nama_lengkap }}" width="40" height="40" class="rounded-circle" style="object-fit: cover;">
                            @else
                                <img src="https://placehold.co/40x40/e9ecef/6c757d?text={{ substr($karyawan->nama_lengkap, 0, 1) }}" alt="Tidak ada foto" class="rounded-circle">
                            @endif
                        </td>
                        <td>{{ $karyawan->nama_lengkap }}</td>
                        <td>{{ $karyawan->nik ?? '-' }}</td>
                        <td>{{ $karyawan->posisi }}</td>
                        <td>{{ $karyawan->user->email ?? '-' }}</td>
                        <td>{{ $karyawan->tahun_masuk ?? '-' }}</td> {{-- Tampilkan Tahun Masuk --}}
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.karyawan.edit', $karyawan->id) }}" class="btn btn-sm" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <form action="{{ route('admin.karyawan.destroy', $karyawan->id) }}" method="POST" class="d-inline delete-karyawan-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data karyawan.</td> {{-- Sesuaikan colspan --}}
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="mt-3">
            {{ $karyawans->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        const table = $('#karyawanTable').DataTable({
            "language": {
                "url": "{{ asset('js/datatables/id.json') }}"
            },
            "responsive": true,
            "paging": false, // Matikan paging bawaan DataTables karena kita pakai Laravel pagination
            "info": false, // Matikan info bawaan DataTables
            "searching": false, // Matikan searching bawaan DataTables karena kita pakai search form sendiri
            "columnDefs": [
                { "searchable": false, "orderable": false, "targets": [0, 1, 7] }, // #, Foto, Aksi
                { "width": "5%", "targets": 0 }, // Lebar kolom #
                { "width": "5%", "targets": 1 } // Lebar kolom Foto
            ],
            "order": [[ 2, "asc" ]] // Urutkan berdasarkan Nama Lengkap (kolom ke-2)
        });

        // Fungsi untuk membuat nomor urut otomatis
        table.on('order.dt search.dt', function () {
            let i = 1;
            table.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                this.data(i++);
            });
        }).draw();

        // SweetAlert untuk konfirmasi hapus karyawan
        $('.delete-karyawan-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data karyawan ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Logika Export PDF/Excel
        $('#exportExcelBtn, #exportPdfBtn').on('click', function(e) {
            e.preventDefault();
            const type = $(this).attr('id') === 'exportExcelBtn' ? 'excel' : 'pdf';
            const baseUrl = "{{ route('admin.karyawan.export') }}";
            const tahunMasukFilter = $('#tahun_masuk_filter').val();
            
            let url = `${baseUrl}?type=${type}`;
            if (tahunMasukFilter) {
                url += `&tahun_masuk=${tahunMasukFilter}`;
            }
            
            window.open(url, '_blank');
        });
    });
    </script>
@endpush