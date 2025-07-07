<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi Karyawan </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .hero-section {
            background: linear-gradient(45deg, #091D70, #3a53c4);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 600;
        }
        .hero-section p {
            font-size: 1.25rem;
            max-width: 600px;
            margin: 20px auto;
        }
        .btn-hero {
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
        }
        .features-section {
            padding: 80px 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 40px 0;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-calendar-check-fill"></i>
                Absensi Karyawan
            </a>
            <div>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-outline-primary me-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>

                        @if (Route::has('register'))
                            {{-- Anda bisa menonaktifkan registrasi jika hanya admin yang boleh mendaftarkan --}}
                            {{-- <a href="{{ route('register') }}" class="btn btn-outline-secondary">Register</a> --}}
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1>Sistem Absensi Modern</h1>
            <p>Ucapkan selamat tinggal pada absensi manual. Lakukan absensi dengan cepat, akurat, dan aman menggunakan verifikasi wajah dan lokasi.</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-hero">Mulai Absen Sekarang <i class="bi bi-arrow-right-circle-fill"></i></a>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Fitur Unggulan Kami</h2>
                <p class="lead text-muted">Dirancang untuk efisiensi dan kemudahan manajemen.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-person-bounding-box"></i>
                        </div>
                        <h4>Verifikasi Wajah</h4>
                        <p>Teknologi pengenalan wajah canggih untuk memastikan identitas karyawan yang benar dan mencegah kecurangan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h4>Validasi Lokasi (GPS)</h4>
                        <p>Pastikan karyawan melakukan absensi dari lokasi yang telah ditentukan dengan radius yang dapat diatur oleh admin.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4>Manajemen Jadwal</h4>
                        <p>Admin dapat dengan mudah mengatur jam masuk, jam pulang, dan batas toleransi keterlambatan untuk semua karyawan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Absensi Karyawan') }}. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
