<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'absensi'))</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="overflow-x:hidden">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-calendar-check"></i> {{ config('app.name', 'Absensi Karyawan') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if(Auth::user()->role == 'admin')
                                {{-- MENU UNTUK ADMIN --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.karyawan.index') }}"><i class="bi bi-people-fill"></i> Karyawan</a>
                                </li>
                                {{-- PERBAIKAN: Menambahkan link ke halaman pengaturan --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.pengaturan.index') }}"><i class="bi bi-gear-fill"></i> Pengaturan</a>
                                </li>

                            @elseif(Auth::user()->role == 'karyawan')
                                {{-- MENU UNTUK KARYAWAN --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('karyawan.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('karyawan.absen.index') }}"><i class="bi bi-camera-video-fill"></i> Absen Wajah</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('karyawan.profil.pendaftaran-wajah') }}"><i class="bi bi-person-vcard"></i> Daftar Wajah</a>
                                </li>
                                {{-- PERBAIKAN: Menambahkan menu riwayat --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('karyawan.riwayat.index') }}"><i class="bi bi-clock-history"></i> Riwayat</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('karyawan.pengajuan.create') }}"> <i class="nav-icon bi bi-circle"></i></i> Ajukan Izin / Sakit</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('karyawan.pengajuan.riwayat') }}">   <i class="nav-icon bi bi-clock-history"></i>Riwayat Pengajuan</a>
                                </li>
                               
                                {{-- ... --}}
                            @endif
                        @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"> @csrf </form>

                                     <a class="dropdown-item" href="{{ route('karyawan.profil.edit') }}">
                                        <i class="bi bi-person-fill"></i> Profil</a>
                                    </a>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
