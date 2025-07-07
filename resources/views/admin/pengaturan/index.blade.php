@extends('admin.layouts.app')

@section('title', 'Pengaturan Absensi')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.css" />
    <style>
        /* PERBAIKAN 1: Menambahkan min-height sebagai failsafe */
        #map { height: 400px; min-height: 400px; width: 100%; border-radius: 8px; z-index: 1; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold">Pengaturan Absensi</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
     @if ($errors->any())
        <div class="alert alert-danger">
            <p><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengaturan.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill me-2"></i>Pengaturan Lokasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="lokasi_kantor" class="form-label">Nama Lokasi/Kantor</label>
                            <input type="text" class="form-control" name="lokasi_kantor" id="lokasi_kantor" value="{{ optional($pengaturan)->lokasi_kantor ?? 'Kantor Pusat' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Lokasi (Cari, Klik, atau Geser Penanda)</label>
                            <div id="map"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" name="latitude" id="latitude" value="{{ optional($pengaturan)->latitude }}" readonly required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="longitude" value="{{ optional($pengaturan)->longitude }}" readonly required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="radius" class="form-label">Radius Absensi (meter)</label>
                            <input type="number" class="form-control" name="radius" id="radius" value="{{ optional($pengaturan)->radius ?? 100 }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Pengaturan Jadwal</h5>
                    </div>
                    <div class="card-body">
                        <p class="fw-bold text-primary">Jadwal Masuk</p>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="jam_masuk_mulai">Mulai Absen Masuk</label>
                                <input type="time" class="form-control" name="jam_masuk_mulai" value="{{ optional($pengaturan)->jam_masuk_mulai ?? '07:00' }}" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="jam_masuk_selesai">Selesai Absen Masuk</label>
                                <input type="time" class="form-control" name="jam_masuk_selesai" value="{{ optional($pengaturan)->jam_masuk_selesai ?? '09:00' }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="batas_terlambat">Batas Toleransi Terlambat</label>
                            <input type="time" class="form-control" name="batas_terlambat" value="{{ optional($pengaturan)->batas_terlambat ?? '08:00' }}" required>
                        </div>
                        <hr>
                        <p class="fw-bold text-success">Jadwal Pulang</p>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="jam_pulang_mulai">Mulai Absen Pulang</label>
                                <input type="time" class="form-control" name="jam_pulang_mulai" value="{{ optional($pengaturan)->jam_pulang_mulai ?? '16:00' }}" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="jam_pulang_selesai">Selesai Absen Pulang</label>
                                <input type="time" class="form-control" name="jam_pulang_selesai" value="{{ optional($pengaturan)->jam_pulang_selesai ?? '18:00' }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save-fill me-2"></i>Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.umd.js"></script>
    <script>
        // PERBAIKAN 2: Membungkus semua logika di dalam window.onload
        window.onload = function() {
            try {
                if (typeof L === 'undefined') {
                    console.error('Library Leaflet (L) tidak ditemukan.');
                    return;
                }

                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                const radiusInput = document.getElementById('radius');

                let defaultLat = {{ optional($pengaturan)->latitude ?? -6.2088 }};
                let defaultLng = {{ optional($pengaturan)->longitude ?? 106.8456 }};

                let map = L.map('map').setView([defaultLat, defaultLng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
                let radiusCircle = L.circle([defaultLat, defaultLng], {
                    radius: parseInt(radiusInput.value) || 100,
                    color: 'blue',
                    fillColor: '#3388ff',
                    fillOpacity: 0.2
                }).addTo(map);
                
                // invalidateSize() tetap digunakan sebagai double-check
                setTimeout(function() {
                    map.invalidateSize();
                }, 100);

                function updatePosition(latlng) {
                    latInput.value = latlng.lat.toFixed(7);
                    lngInput.value = latlng.lng.toFixed(7);
                    marker.setLatLng(latlng);
                    radiusCircle.setLatLng(latlng);
                }

                updatePosition(marker.getLatLng());

                marker.on('dragend', function(event) { updatePosition(marker.getLatLng()); });
                map.on('click', function(e) { updatePosition(e.latlng); });
                
                radiusInput.addEventListener('input', function(e) {
                    const newRadius = parseInt(e.target.value);
                    if (!isNaN(newRadius) && newRadius > 0) {
                        radiusCircle.setRadius(newRadius);
                    }
                });

                const search = new GeoSearch.GeoSearchControl({
                    provider: new GeoSearch.OpenStreetMapProvider(),
                    style: 'bar', autoClose: true, searchLabel: 'Cari alamat atau nama tempat',
                });
                map.addControl(search);

                map.on('geosearch/showlocation', function(result) {
                    const newPos = { lat: result.location.y, lng: result.location.x };
                    updatePosition(newPos);
                });

            } catch (error) {
                console.error('Terjadi error saat menginisialisasi peta:', error);
                alert('Gagal memuat peta. Cek console (F12) untuk detail error.');
            }
        };
    </script>
@endpush
