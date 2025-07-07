@extends('layouts.app')

@section('title', 'Absensi Wajah & Lokasi')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.css" />
    <style>
        .webcam-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: auto;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        #overlay { position: absolute; top: 0; left: 0; }
        #map { height: 350px; width: 100%; border-radius: 12px; }
        @media (max-width: 576px) {
            #absenMasukBtn, #absenPulangBtn { font-size: 15px; }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body py-2">
                    <h3 id="clock" class="my-1 fs-2 fw-bold" style="color: #091D70;"></h3>
                    <p class="fs-6 text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center p-4">
                    <h4 class="mb-3">Verifikasi Wajah</h4>
                    <div id="face-container" class="d-none">
                        <div class="webcam-container">
                            <video id="webcam" class="w-100 h-100" autoplay muted playsinline></video>
                            <canvas id="overlay"></canvas>
                        </div>
                        <div id="status-message" class="alert alert-info mt-3">Memuat sistem pengenalan wajah...</div>
                    </div>
                    <div class="mt-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button id="absenMasukBtn" class="btn btn-lg text-white" style="background-color: #091D70;" disabled><i class="bi bi-box-arrow-in-right me-2"></i>Absen Masuk</button>
                            <button id="absenPulangBtn" class="btn btn-success btn-lg" disabled><i class="bi bi-box-arrow-in-left me-2"></i>Absen Pulang</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h4 class="card-title mb-3">Verifikasi Lokasi</h4>
                    <div id="location-status" class="alert alert-warning">Mencari lokasi Anda... Pastikan GPS aktif.</div>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        window.onload = function() {
            
            const pengaturan = @json($pengaturan ?? null);
            if (!pengaturan) {
                document.body.innerHTML = `<div class="alert alert-danger text-center m-5"><h1>Error</h1><p>Pengaturan absensi belum diatur oleh Admin. Silakan hubungi admin untuk konfigurasi.</p></div>`;
                return;
            }

            const LOKASI_KANTOR = { 
                lat: parseFloat(pengaturan?.latitude) || 0, 
                lng: parseFloat(pengaturan?.longitude) || 0 
            };
            const RADIUS_METER = parseInt(pengaturan?.radius) || 100;
            
            let userCoords = {};
            let map, userMarker, schoolCircle;
            let faceApiHasRun = false;

            const locationStatusEl = document.getElementById('location-status');
            const faceContainerEl = document.getElementById('face-container');
            const webcamElement = document.getElementById('webcam');
            const canvasElement = document.getElementById('overlay');
            const statusMessage = document.getElementById('status-message');
            const absenMasukBtn = document.getElementById('absenMasukBtn');
            const absenPulangBtn = document.getElementById('absenPulangBtn');
            
            const referenceImageUrl = @json($fotoReferensiUrl);
            let referenceFaceMatcher;

            function initMap() {
                map = L.map('map').setView(LOKASI_KANTOR, 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker(LOKASI_KANTOR).addTo(map).bindPopup('<b>Lokasi Kantor</b>').openPopup();
                schoolCircle = L.circle(LOKASI_KANTOR, { color: 'blue', fillColor: '#3388ff', fillOpacity: 0.2, radius: RADIUS_METER }).addTo(map);
                
                setTimeout(() => map.invalidateSize(), 100);
            }

            function handleLocation(position) {
                userCoords = { lat: position.coords.latitude, lng: position.coords.longitude };
                if (!userMarker) {
                    userMarker = L.marker(userCoords, {icon: L.icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]})}).addTo(map).bindPopup('Lokasi Anda');
                } else {
                    userMarker.setLatLng(userCoords);
                }
                
                map.invalidateSize();
                map.panTo(userCoords);

                const distance = map.distance(userCoords, LOKASI_KANTOR);
                
                if (distance <= RADIUS_METER) {
                    locationStatusEl.className = 'alert alert-success';
                    locationStatusEl.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Anda berada di dalam jangkauan (${Math.round(distance)} meter).`;
                    faceContainerEl.classList.remove('d-none');
                    runFaceApi();
                } else {
                    locationStatusEl.className = 'alert alert-danger';
                    locationStatusEl.innerHTML = `<i class="bi bi-x-circle-fill me-2"></i>Anda berada di luar jangkauan (${Math.round(distance)} meter). Absen dinonaktifkan.`;
                    faceContainerEl.classList.add('d-none');
                    absenMasukBtn.disabled = true;
                    absenPulangBtn.disabled = true;
                }
            }

            function handleLocationError(error) {
                locationStatusEl.className = 'alert alert-danger';
                locationStatusEl.innerText = 'Gagal mendapatkan lokasi. Pastikan GPS dan izin lokasi aktif.';
            }

            async function runFaceApi() {
                if (faceApiHasRun) return;
                faceApiHasRun = true;
                if (!referenceImageUrl) {
                    updateStatus('Error: Foto referensi tidak ditemukan. Silakan daftar wajah atau upload foto profil.', false, 'danger');
                    return;
                }
                try {
                    updateStatus('Memuat pengenalan wajah...');
                    await Promise.all([
                        faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                    ]);
                    updateStatus('Menganalisis foto referensi...');
                    const referenceImage = await faceapi.fetchImage(referenceImageUrl);
                    const referenceDetection = await faceapi.detectSingleFace(referenceImage).withFaceLandmarks().withFaceDescriptor();
                    if (!referenceDetection) throw new Error('Gagal memproses foto referensi. Gunakan foto yang lebih jelas.');
                    referenceFaceMatcher = new faceapi.FaceMatcher([referenceDetection.descriptor], 0.5);
                    updateStatus('Menyalakan kamera...');
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    webcamElement.srcObject = stream;
                    webcamElement.onloadedmetadata = () => onPlay();
                } catch (e) {
                    updateStatus(`Error Wajah: ${e.message}`, false, 'danger');
                }
            }

            function onPlay() {
                updateStatus('Kamera aktif. Arahkan wajah ke kamera.', false);
                const displaySize = { width: webcamElement.clientWidth, height: webcamElement.clientHeight };
                faceapi.matchDimensions(canvasElement, displaySize);
                const detectionInterval = setInterval(async () => {
                    if (!webcamElement.srcObject) { clearInterval(detectionInterval); return; }
                    const detections = await faceapi.detectAllFaces(webcamElement, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                    if (detections.length > 0) {
                        const bestMatch = referenceFaceMatcher.findBestMatch(detections[0].descriptor);
                        if (bestMatch.label !== 'unknown') {
                            updateStatus('<i class="bi bi-check-circle-fill me-2"></i>Wajah cocok! Silakan absen.', false, 'success');
                            absenMasukBtn.disabled = false;
                            absenPulangBtn.disabled = false;
                        } else {
                            updateStatus('<i class="bi bi-exclamation-triangle-fill me-2"></i>Wajah terdeteksi tapi tidak cocok.', false, 'warning');
                            absenMasukBtn.disabled = true;
                            absenPulangBtn.disabled = true;
                        }
                    } else {
                        updateStatus('Arahkan wajah ke kamera...', false, 'info');
                    }
                }, 800);
            }

            async function sendAttendance(type) {
                absenMasukBtn.disabled = true;
                absenPulangBtn.disabled = true;
                updateStatus('Mengirim data absensi...');
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = webcamElement.videoWidth;
                tempCanvas.height = webcamElement.videoHeight;
                tempCanvas.getContext('2d').drawImage(webcamElement, 0, 0);
                const imageDataUrl = tempCanvas.toDataURL('image/jpeg');
                
                // --- PERBAIKAN UTAMA ADA DI SINI ---
                const url = (type === 'masuk') ? '{{ route("karyawan.absen.masuk") }}' : '{{ route("karyawan.absen.pulang") }}';
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ image: imageDataUrl, latitude: userCoords.lat, longitude: userCoords.lng })
                    });
                    const result = await response.json();
                    if (response.ok) {
                        Swal.fire({ title: 'Berhasil!', text: result.message, icon: 'success' })
                           // PERBAIKAN 2: Mengarahkan ke route dashboard yang benar
                           .then(() => window.location.href = '{{ route("karyawan.dashboard") }}');
                    } else { throw new Error(result.message); }
                } catch (error) {
                    Swal.fire({ title: 'Oops...', text: error.message, icon: 'error' });
                    absenMasukBtn.disabled = false;
                    absenPulangBtn.disabled = false;
                }
            }

            function updateStatus(text, showSpinner = true, alertType = 'info') {
                let spinner = showSpinner ? `<div class="spinner-border spinner-border-sm me-2"></div>` : '';
                statusMessage.innerHTML = `${spinner}${text}`;
                statusMessage.className = `alert alert-${alertType} mt-3`;
            }

            initMap();
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(handleLocation, handleLocationError, { enableHighAccuracy: true });
            } else {
                locationStatusEl.className = 'alert alert-danger';
                locationStatusEl.innerText = 'Browser Anda tidak mendukung Geolocation.';
            }
            absenMasukBtn.addEventListener('click', () => sendAttendance('masuk'));
            absenPulangBtn.addEventListener('click', () => sendAttendance('pulang'));
            document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID');
            setInterval(() => { document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID'); }, 1000);
        };
    </script>
@endpush
