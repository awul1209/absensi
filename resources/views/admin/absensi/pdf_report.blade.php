<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Absensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px; /* Ukuran font lebih kecil untuk banyak kolom */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px; /* Padding lebih kecil */
            text-align: left;
            word-wrap: break-word; /* Memastikan teks panjang bisa wrap */
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px; /* Ukuran heading lebih kecil */
        }
        .filter-info {
            margin-bottom: 10px;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            color: #fff; /* Default text color */
        }
        .badge-hadir { background-color: #28a745; }
        .badge-terlambat { background-color: #ffc107; color: #333;}
        .badge-pulang-cepat { background-color: #dc3545; }
        .badge-izin { background-color: #007bff; }
        .badge-sakit { background-color: #fd7e14; }
        .badge-tidak-masuk { background-color: #6c757d; }
        .badge-secondary { background-color: #6c757d; }
        .photo-thumb {
            width: 30px; /* Ukuran thumbnail lebih kecil untuk PDF */
            height: 30px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Absensi Karyawan</h1>
    </div>

    @if(!empty($filterInfo))
        <div class="filter-info">
            <strong>Filter:</strong> {{ implode(', ', $filterInfo) }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center">No.</th>
                <th>Tanggal</th>
                <th>Nama Karyawan</th>
                <th>NIK</th>
                <th>Status Harian</th>
                <th>Jam Masuk</th>
                <th>Status Masuk</th>
                <th>Keterlambatan (Menit)</th>
                <th>Foto Masuk</th>
                <th>Lokasi Masuk</th>
                <th>Jam Pulang</th>
                <th>Foto Pulang</th>
                <th>Lokasi Pulang</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($absensis as $index => $absensi)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $absensi->tanggal ? \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y') : '-' }}</td>
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
                <td>{{ $absensi->keterlambatan_menit ?? '0' }}</td>
                <td>
                    @if($absensi->foto_masuk)
                        <img src="{{ public_path('storage/' . $absensi->foto_masuk) }}" alt="Foto Masuk" class="photo-thumb">
                    @else
                        -
                    @endif
                </td>
                <td>{{ ($absensi->latitude_masuk && $absensi->longitude_masuk) ? $absensi->latitude_masuk . ', ' . $absensi->longitude_masuk : '-' }}</td>
                <td>{{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i:s') : '-' }}</td>
                <td>
                    @if($absensi->foto_pulang)
                        <img src="{{ public_path('storage/' . $absensi->foto_pulang) }}" alt="Foto Pulang" class="photo-thumb">
                    @else
                        -
                    @endif
                </td>
                <td>{{ ($absensi->latitude_pulang && $absensi->longitude_pulang) ? $absensi->latitude_pulang . ', ' . $absensi->longitude_pulang : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="text-center">Tidak ada data absensi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>