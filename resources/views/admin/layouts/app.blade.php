<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ config('app.name') }}</title>

    {{-- 1. Pindahkan CSS library ke atas --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- 2. Letakkan stack untuk CSS halaman di sini (setelah library) dan hapus @endpush --}}
    @stack('styles')

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7fc;
        }
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background-color: #091D70; /* Warna biru tua */
            color: #fff;
            transition: all 0.3s;
            position: fixed;
            height: 100%;
            z-index: 1000;
        }
        .sidebar .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar .sidebar-header h3 {
            color: #fff;
            font-weight: 600;
        }
        .sidebar-nav {
            padding: 20px 0;
        }
        .sidebar-nav .nav-link {
            color: #adb5bd;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        .sidebar-nav .nav-link i {
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #0d6efd;
        }
        .main-content {
            width: 100%;
            padding: 20px;
            margin-left: 260px;
            transition: all 0.3s;
        }
        .navbar-admin {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        /* Style untuk sidebar saat diciutkan */
        .sidebar.collapsed {
            width: 0;
            overflow: hidden;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        .sidebar-toggler {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
        }

        /* Tampilan Responsif */
        @media (max-width: 992px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.active {
                width: 260px;
            }
            .sidebar-toggler {
                display: block;
            }
            #judul-panel{
                font-size:18px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3 style="font-size:22px;"><i class="bi bi-shield-lock-fill"></i> Panel Admin</h3>
            </div>

            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item">
                    {{-- Ganti 'admin.dashboard' dengan nama route Anda jika berbeda --}}
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Ganti 'admin.karyawan.*' dengan nama route Anda jika berbeda --}}
                    <a class="nav-link {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}" href="{{ route('admin.karyawan.index') }}">
                        <i class="bi bi-people-fill"></i>
                        Manajemen Karyawan
                    </a>
                </li>
                 <li class="nav-item">
                    {{-- Buat route untuk halaman ini nanti --}}
                    <a class="nav-link {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}" href="{{ route('admin.absensi.index') }}">
                        <i class="bi bi-calendar2-check-fill"></i>
                        Data Absensi
                    </a>
                </li>
                 <li class="nav-item">
                    {{-- Buat route untuk halaman ini nanti --}}
                    <a class="nav-link {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}" href="{{ route('admin.pengajuan.index') }}">
                        <i class="bi bi-calendar2-check-fill"></i>
                        Manajemen Pengajuan
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Ganti 'admin.pengaturan.index' dengan nama route Anda jika berbeda --}}
                    <a class="nav-link {{ request()->routeIs('admin.pengaturan.index') ? 'active' : '' }}" href="{{ route('admin.pengaturan.index') }}">
                        <i class="bi bi-gear-fill"></i>
                        Pengaturan Absensi
                    </a>
                </li>
                <li class="nav-item mt-auto">
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-left"></i>
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        
        <div id="main-content" class="main-content">
            <nav class="navbar navbar-expand-lg navbar-light navbar-admin mb-4">
                <div class="container-fluid">
                    <button id="sidebar-toggler" class="sidebar-toggler">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="ms-auto">
                         <span class="navbar-text">
                            Selamat datang, **{{ Auth::user()->name }}**!
                         </span>
                    </div>
                </div>
            </nav>

            {{-- Area konten dinamis --}}
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggler = document.getElementById('sidebar-toggler');

            toggler.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });
        });
    </script>
    
    {{-- 3. TAMBAHKAN STACK UNTUK SCRIPT DI SINI --}}
    @stack('scripts')
</body>
</html>