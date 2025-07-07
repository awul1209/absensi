@extends('admin.layouts.app') {{-- Pastikan ini mengarah ke layout admin Anda --}}

@section('title', 'Data Absensi Karyawan')

@push('styles')
{{-- CSS DataTables Responsive --}}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"/>

<style>
    /* Styling umum dari layout admin Anda */
    .card-header {
        border-bottom: 1px solid #dee2e6;
    }
    #absensiTable th, #absensiTable td {
        font-size: 13px;
        vertical-align: middle;
        padding: 0.5rem 0.75rem; /* Sesuaikan padding agar tidak terlalu renggang */
        /* Hapus white-space: nowrap; untuk memungkinkan DataTables Responsive berfungsi */
        /* white-space: nowrap; */ /* BARIS INI DIHAPUS/DIKOMENTARI */
    }
    /* Khusus untuk kolom yang DataTables Responsive tambahkan, seperti kolom kontrol */
    table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before,
    table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
        left: 0; /* Sesuaikan posisi tombol + */
    }
    table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before,
    table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th.dtr-control:before {
        content: "\f068"; /* Font Awesome icon for minus, jika menggunakan Font Awesome */
        /* Atau pastikan ikon default Bootstrap (misalnya bi-dash-circle) muncul jika tidak ada Font Awesome */
    }


    #absensiTable th {
        font-weight: 600;
        /* white-space: nowrap; */ /* BARIS INI DIHAPUS/DIKOMENTARI */
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
        color: #495057;
        font-size: 18px;
    }
    .btn-group .btn:hover {
        background-color: rgba(0, 0, 0, 0.07);
    }
    /* Warna spesifik untuk ikon */
    .btn-group .btn .bi-download { color: #091D70; }


    /* Warna badge status */
    .badge-hadir { background-color: #28a745; color: white; } /* Hijau */
    .badge-terlambat { background-color: #ffc107; color: #333; } /* Kuning */
    .badge-pulang-cepat { background-color: #dc3545; color: white; } /* Merah */
    .badge-izin { background-color: #007bff; color: white; } /* Biru */
    .badge-sakit { background-color: #fd7e14; color: white; } /* Oranye */
    .badge-tidak-masuk { background-color: #6c757d; color: white; } /* Abu-abu */
    .badge-belum-pulang { background-color: #17a2b8; color: white; } /* Biru muda */
    .badge-secondary { background-color: #6c757d; color: white; } /* Default */

    .photo-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        display: block;
        margin: auto;
    }

    /* ===== PERBAIKAN SCROLLBAR DAN RESPONSIFITAS HEADER (Diadaptasi dari index karyawan) ===== */
    /* Mencegah scrollbar horizontal pada seluruh body halaman */
    html, body {
        overflow-x: hidden !important; /* Pastikan tidak ada scrollbar horizontal di level HTML/body */
        width: 100% !important; /* Pastikan lebar HTML/body 100% dari viewport */
    }

    /* Pastikan .card tidak melebihi lebar kontainer parent-nya dan handle overflow di dalamnya */
    .card {
        max-width: 100%; /* Memastikan card tidak melebihi lebar kontainer parent */
        overflow: hidden; /* Sembunyikan overflow di dalam card jika ada, agar tidak keluar */
    }

    /* Mengatur tinggi dan padding untuk form control small */
    .form-control-sm, .form-select-sm {
        height: calc(1.8125rem + 2px);
        padding: .25rem .5rem;
        font-size: .875rem;
    }

    /* Responsifitas header filter/export - Mengikuti pola dari index.blade.php karyawan */
    @media (max-width: 767.98px) { /* Breakpoint medium atau lebih kecil */
        .card-header .d-flex.flex-wrap {
            flex-direction: column;
            align-items: stretch !important; /* Regangkan item agar memenuhi lebar penuh */
            gap: 0.5rem; /* Menyesuaikan gap untuk mode kolom */
        }
        /* Mengambil lebar penuh untuk setiap item di filter/export pada layar kecil */
        .card-header .d-flex.flex-wrap > *,
        .card-header .d-flex.flex-wrap .form-select,
        .card-header .d-flex.flex-wrap .form-control {
            width: 100% !important; /* Ambil lebar penuh */
            margin-bottom: 0; /* Jarak antar item sudah dihandle oleh 'gap' */
        }
        /* Jika ada btn-group untuk export, pastikan tetap sebaris dan mengisi penuh */
        .card-header .d-flex.flex-wrap .btn-group {
            flex-direction: row;
            width: 100% !important;
        }
        .card-header .d-flex.flex-wrap .btn-group .btn {
            flex-grow: 1; /* Biarkan tombol export memenuhi */
        }
    }
    
    /* Memastikan tabel responsif (jika DataTables Responsive tidak aktif) memiliki scrollbar sendiri */
    /* Namun dengan responsive:true, DataTables akan mengelola ini. */
    .table-responsive {
        overflow-x: auto; /* Pastikan scrollbar muncul hanya di dalam tabel jika dibutuhkan */
        width: 100%; /* Pastikan kontainer responsif tabel mengambil lebar penuh */
    }
</style>
@endpush

@section('content')
{{-- Tambahkan container-fluid di sini untuk mengontrol lebar konten secara global --}}
<div class="container-fluid">
    <div class="card shadow-sm">
        {{-- Menggunakan flex-md-row agar layout header menjadi baris lebih awal (dari breakpoint medium) --}}
        <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
            <h5 class="mb-3 mb-md-0 fw-bold" style="color: #091D70;">
                <i class="bi bi-calendar-check me-2"></i>Data Absensi Karyawan
            </h5>
            
            {{-- Kontainer untuk filter dan tombol aksi, dengan flex-wrap dan gap --}}
            <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
                {{-- Form Filter Tanggal dan Karyawan --}}
                <form action="{{ route('admin.absensi.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="karyawan_id" id="karyawan_filter" class="form-select form-select-sm" style="width: auto; min-width: 150px;">
                        <option value="">Filter Karyawan</option>
                        @foreach ($karyawanOptions as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ (string)request('karyawan_id') === (string)$karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>

                    <label for="start_date_filter" class="form-label mb-0 visually-hidden">Dari Tanggal:</label>
                    <input type="date" name="start_date" id="start_date_filter" class="form-control form-control-sm" placeholder="Dari Tanggal" value="{{ request('start_date') }}" style="width: auto;">
                    
                    <label for="end_date_filter" class="form-label mb-0 visually-hidden">Sampai Tanggal:</label>
                    <input type="date" name="end_date" id="end_date_filter" class="form-control form-control-sm" placeholder="Sampai Tanggal" value="{{ request('end_date') }}" style="width: auto;">
                    
                    <button type="submit" class="btn btn-custom-primary btn-sm">Filter</button>
                    <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </form>
                
                {{-- Tombol Export --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" id="exportAbsensiExcelBtn">Export ke Excel</a></li>
                        <li><a class="dropdown-item" href="#" id="exportAbsensiPdfBtn">Export ke PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="absensiTable" class="table table-hover dt-responsive nowrap" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tanggal</th>
                            <th>Nama Karyawan</th>
                            <th>NIK</th>
                            <th>Status Harian</th>
                            <th>Jam Masuk</th>
                            <th>Status Masuk</th>
                            <th>Keterlambatan</th>
                            <th>Foto Masuk</th>
                            <th>Lokasi Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Lokasi Pulang</th>
                            {{-- <th>Aksi</th> --}} </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensis as $absensi)
                        <tr>
                            <td class="text-center"></td>
                            <td>{{ $absensi->tanggal ? \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                            <td>{{ $absensi->karyawan->nama_lengkap ?? '-' }}</td>
                            <td>{{ $absensi->karyawan->nik ?? '-' }}</td>
                            <td>
                                @php
                                    $statusHarian = strtolower($absensi->status ?? '');
                                    $badgeClass = '';
                                    switch ($statusHarian) {
                                        case 'hadir': $badgeClass = 'badge-hadir'; break;
                                        case 'terlambat': $badgeClass = 'badge-terlambat'; break;
                                        case 'izin': $badgeClass = 'badge-izin'; break;
                                        case 'sakit': $badgeClass = 'badge-sakit'; break;
                                        case 'tidak_masuk': $badgeClass = 'badge-tidak-masuk'; break;
                                        default: $badgeClass = 'badge-secondary'; break;
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($statusHarian) }}</span>
                            </td>
                            <td>{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i:s') : '-' }}</td>
                            <td>
                                @php
                                    $statusMasuk = strtolower($absensi->status_masuk ?? '');
                                    $badgeMasukClass = '';
                                    if ($statusMasuk == 'hadir') {
                                        $badgeMasukClass = 'badge-hadir';
                                    } elseif ($statusMasuk == 'terlambat') {
                                        $badgeMasukClass = 'badge-terlambat';
                                    } else {
                                        $badgeMasukClass = 'badge-secondary';
                                    }
                                @endphp
                                <span class="badge {{ $badgeMasukClass }}">{{ $absensi->status_masuk ? ucfirst($absensi->status_masuk) : '-' }}</span>
                            </td>
                            <td>
                                @if($absensi->keterlambatan_menit > 0)
                                    <span class="badge badge-terlambat">{{ $absensi->keterlambatan_menit }} menit</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($absensi->foto_masuk)
                                    <a href="{{ asset('storage/' . $absensi->foto_masuk) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $absensi->foto_masuk) }}" alt="Foto Masuk" class="photo-thumb">
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($absensi->latitude_masuk && $absensi->longitude_masuk)
                                    {{ $absensi->latitude_masuk }}, {{ $absensi->longitude_masuk }}
                                    <br><a href="http://maps.google.com/?q={{ $absensi->latitude_masuk }},{{ $absensi->longitude_masuk }}" target="_blank" title="Lihat di Peta"><i class="bi bi-geo-alt-fill"></i></a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i:s') : '-' }}</td>
                            <td>
                                @if($absensi->foto_pulang)
                                    <a href="{{ asset('storage/' . $absensi->foto_pulang) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $absensi->foto_pulang) }}" alt="Foto Pulang" class="photo-thumb">
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($absensi->latitude_pulang && $absensi->longitude_pulang)
                                    {{ $absensi->latitude_pulang }}, {{ $absensi->longitude_pulang }}
                                    <br><a href="http://maps.google.com/?q={{ $absensi->latitude_pulang }},{{ $absensi->longitude_pulang }}" target="_blank" title="Lihat di Peta"><i class="bi bi-geo-alt-fill"></i></a>
                                @else
                                    -
                                @endif
                            </td>
                            {{-- <td>
                                <a href="{{ route('admin.absensi.show', $absensi->id) }}" class="btn btn-sm btn-info" title="Detail"><i class="bi bi-eye"></i></a>
                            </td> --}}
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted">Tidak ada data absensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="mt-3">
                {{ $absensis->appends(request()->query())->links() }} {{-- appends agar filter tetap saat pagination --}}
            </div>
        </div>
    </div>
</div> {{-- Tutup container-fluid --}}
@endsection

@push('scripts')
    {{-- JavaScript DataTables dan JQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    {{-- JavaScript DataTables Responsive --}}
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        const table = $('#absensiTable').DataTable({
            "language": {
                "url": "/js/datatables/id.json" // Menggunakan path absolut, ini seringkali memecahkan masalah
            },
            "responsive": true, // Aktifkan DataTables Responsive
            "paging": false, // Matikan paging bawaan DataTables karena kita pakai Laravel pagination
            "info": false, // Matikan info bawaan DataTables
            "searching": false, // Matikan searching bawaan DataTables karena kita pakai search form sendiri
            "ordering": false, // Matikan sorting bawaan DataTables karena kita sudah urutkan di controller
            "columnDefs": [
                { "searchable": false, "orderable": false, "targets": [0, 8, 9, 11, 12] }, // Kolom #, Foto, Lokasi
                // Atur prioritas responsif (kolom mana yang disembunyikan duluan)
                // Angka lebih kecil = prioritas lebih tinggi (lebih mungkin tetap terlihat)
                { responsivePriority: 1, targets: 0 },   // #
                { responsivePriority: 2, targets: 1 },   // Tanggal
                { responsivePriority: 3, targets: 2 },   // Nama Karyawan
                { responsivePriority: 4, targets: 5 },   // Jam Masuk
                { responsivePriority: 5, targets: 6 },   // Status Masuk
                { responsivePriority: 6, targets: 10 },  // Jam Pulang
                { responsivePriority: 7, targets: 3 },   // NIK
                { responsivePriority: 8, targets: 4 },   // Status Harian
                { responsivePriority: 9, targets: 7 },   // Keterlambatan
                { responsivePriority: 10, targets: 8 },  // Foto Masuk
                { responsivePriority: 11, targets: 9 },  // Lokasi Masuk
                { responsivePriority: 12, targets: 11 }, // Foto Pulang
                { responsivePriority: 13, targets: 12 }  // Lokasi Pulang
            ],
        });

        // Fungsi untuk membuat nomor urut otomatis
        table.on('order.dt search.dt', function () {
            let i = 1;
            table.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                this.data(i++);
            });
        }).draw();
        
        // Atur nilai default untuk filter tanggal jika belum ada pada saat load pertama
        const startDateInput = $('#start_date_filter');
        const endDateInput = $('#end_date_filter');
        const karyawanFilter = $('#karyawan_filter');

        // Hanya set default jika tidak ada nilai yang sudah dipilih sebelumnya dari request
        if (!startDateInput.val() && !endDateInput.val() && !karyawanFilter.val()) {
            const today = new Date();
            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            startDateInput.val(firstDayOfMonth.toISOString().split('T')[0]);
            endDateInput.val(today.toISOString().split('T')[0]);
        }

        // Logika Export PDF/Excel untuk Absensi
        $('#exportAbsensiExcelBtn, #exportAbsensiPdfBtn').on('click', function(e) {
            e.preventDefault();
            const type = $(this).attr('id') === 'exportAbsensiExcelBtn' ? 'excel' : 'pdf';
            const baseUrl = "{{ route('admin.absensi.export') }}"; // Rute export absensi
            const startDateFilter = $('#start_date_filter').val();
            const endDateFilter = $('#end_date_filter').val();
            const karyawanIdFilter = $('#karyawan_filter').val();
            
            let url = `${baseUrl}?type=${type}`;
            if (startDateFilter) {
                url += `&start_date=${startDateFilter}`;
            }
            if (endDateFilter) {
                url += `&end_date=${endDateFilter}`;
            }
            if (karyawanIdFilter) {
                url += `&karyawan_id=${karyawanIdFilter}`;
            }
            
            window.open(url, '_blank');
        });
    });
    </script>
@endpush