@extends('layouts.app')

@section('title', 'Pendaftaran Wajah')

@push('styles')
<style>
    .webcam-container {
        position: relative;
        width: 100%;
        max-width: 500px; /* Batasi lebar maksimum kamera */
        margin: auto;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #dee2e6;
        background-color: #000;
    }
    #canvas-reg {
        position: absolute;
        top: 0;
        left: 0;
    }
    .btn-custom-capture {
        background-color: #091D70;
        border-color: #091D70;
        color: white;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
    }
    .btn-custom-capture:hover {
        background-color: #07175a;
        border-color: #07175a;
        color: white;
    }
    .btn-custom-capture:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    @media (max-width: 576px) {
        #captureBtn { font-size: 15px; }
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-header bg-white border-0 pt-4">
                    <i class="bi bi-camera-video-fill fs-1" style="color: #091D70;"></i>
                    <h4 class="card-title mt-2 mb-0 fw-bold">Pendaftaran Wajah</h4>
                    <p class="text-muted">Foto ini akan menjadi referensi utama untuk absensi.</p>
                </div>
                <div class="card-body px-4">
                    <div id="status-reg" class="alert alert-info">Mempersiapkan sistem...</div>
                    
                    <div class="webcam-container">
                        <video id="webcam-reg" width="400" height="300" autoplay muted playsinline class="w-100 h-100"></video>
                        <canvas id="canvas-reg"></canvas>
                    </div>

                    <div class="d-grid mt-4">
                        <button id="captureBtn" class="btn btn-custom-capture btn-lg" disabled>
                            <i class="bi bi-camera-fill me-2"></i>Ambil & Simpan Wajah
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-muted small pb-3">
                    Pastikan hanya ada satu wajah yang terdeteksi di dalam frame kamera.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Memuat library SweetAlert2 dan Face API --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const webcamElement = document.getElementById('webcam-reg');
            const canvasElement = document.getElementById('canvas-reg');
            const captureBtn = document.getElementById('captureBtn');
            const statusEl = document.getElementById('status-reg');

            // --- FUNGSI BARU untuk update status dengan lebih detail ---
            function updateStatus(message, type = 'info') {
                statusEl.className = `alert alert-${type}`;
                let spinner = type === 'info' ? '<div class="spinner-border spinner-border-sm me-2"></div>' : '';
                statusEl.innerHTML = `${spinner}${message}`;
            }

            async function setupCamera() {
                // --- PERBAIKAN: Cek dukungan browser ---
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    updateStatus('Browser Anda tidak mendukung akses kamera.', 'danger');
                    Swal.fire('Error!', 'Browser Anda tidak mendukung akses kamera. Gunakan browser modern seperti Chrome atau Firefox.', 'error');
                    return;
                }

                try {
                    // 1. Memuat model AI
                    updateStatus('Memuat model pengenalan wajah...');
                    await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                    
                    // 2. Meminta izin kamera
                    updateStatus('Meminta izin akses kamera...');
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    webcamElement.srcObject = stream;

                } catch (err) {
                    // --- PERBAIKAN: Penanganan error yang lebih spesifik ---
                    if (err.name === "NotAllowedError" || err.name === "PermissionDeniedError") {
                        updateStatus('Akses kamera diblokir. Izinkan di pengaturan browser.', 'danger');
                        Swal.fire('Akses Diblokir', 'Anda telah memblokir akses kamera. Mohon izinkan akses kamera melalui ikon gembok (🔒) di address bar browser Anda, lalu refresh halaman.', 'warning');
                    } else {
                        updateStatus('Error: Tidak dapat mengakses kamera.', 'danger');
                        Swal.fire('Error Kamera', `Terjadi masalah saat mengakses kamera: ${err.message}`, 'error');
                    }
                }
            }

            webcamElement.onloadedmetadata = () => {
                updateStatus('Kamera aktif. Posisikan wajah Anda.', 'primary');
                const displaySize = { width: webcamElement.clientWidth, height: webcamElement.clientHeight };
                faceapi.matchDimensions(canvasElement, displaySize);

                const detectionInterval = setInterval(async () => {
                    const detections = await faceapi.detectAllFaces(webcamElement, new faceapi.TinyFaceDetectorOptions());
                    
                    canvasElement.getContext('2d').clearRect(0, 0, canvasElement.width, canvasElement.height);
                    
                    if (detections.length === 1) {
                        // Gambar kotak jika wajah terdeteksi
                        const resizedDetections = faceapi.resizeResults(detections, displaySize);
                        faceapi.draw.drawDetections(canvasElement, resizedDetections);

                        updateStatus('Wajah terdeteksi! Silakan simpan foto Anda.', 'success');
                        captureBtn.disabled = false;
                    } else if (detections.length > 1) {
                        updateStatus('Terdeteksi lebih dari satu wajah. Harap posisikan hanya satu wajah.', 'warning');
                        captureBtn.disabled = true;
                    } else {
                        updateStatus('Posisikan satu wajah di tengah kamera.', 'primary');
                        captureBtn.disabled = true;
                    }
                }, 500);
            };
            
            captureBtn.addEventListener('click', async () => {
                captureBtn.disabled = true;
                updateStatus('Mengambil gambar dan menyimpan...');

                const canvas = document.createElement('canvas');
                canvas.width = webcamElement.videoWidth;
                canvas.height = webcamElement.videoHeight;
                canvas.getContext('2d').drawImage(webcamElement, 0, 0);
                const imageDataUrl = canvas.toDataURL('image/jpeg');

                try {
                    const response = await fetch('{{ route("karyawan.profil.simpan-wajah") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ image: imageDataUrl })
                    });
                    const result = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: result.message,
                            icon: 'success',
                            confirmButtonText: 'Ke Halaman Dashboard'
                        }).then(() => {
                            window.location.href = '{{ route("karyawan.dashboard") }}';
                        });
                    } else { 
                        throw new Error(result.message || 'Gagal menyimpan data.'); 
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Oops...',
                        text: 'Terjadi kesalahan: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'Coba Lagi'
                    });
                    captureBtn.disabled = false; // Aktifkan lagi tombol jika gagal
                    updateStatus('Gagal menyimpan. Silakan coba lagi.', 'danger');
                }
            });

            // Jalankan fungsi utama
            setupCamera();
        });
    </script>
@endpush
