<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
            font-size: 20px;
        }
        .filter-info {
            margin-bottom: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Karyawan</h1>
    </div>

    @if($tahunMasuk)
        <div class="filter-info">
            <strong>Filter Tahun Masuk:</strong> {{ $tahunMasuk }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>Posisi</th>
                <th>Email</th>
                <th>Tahun Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($karyawans as $index => $karyawan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $karyawan->nama_lengkap }}</td>
                <td>{{ $karyawan->nik ?? '-' }}</td>
                <td>{{ $karyawan->posisi }}</td>
                <td>{{ $karyawan->user->email ?? '-' }}</td>
                <td>{{ $karyawan->tahun_masuk ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data karyawan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>