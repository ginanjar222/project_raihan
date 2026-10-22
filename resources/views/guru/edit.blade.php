@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid px-4 py-3">
    <!-- Updated header to match the design with proper blue gradient -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); border-radius: 15px;">
                <div class="card-body text-center py-4">
                    <h4 class="text-white mb-1 fw-bold">
                        <i class="fas fa-user-edit me-2"></i>
                        Edit Data Guru & Staff
                    </h4>
                    <p class="text-white mb-0 opacity-90">Sistem Absensi Guru & Staff - SMA YKBBB LELES</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Added tabbed interface matching the design -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <div class="d-flex border-bottom">
                        <button class="btn btn-link text-decoration-none px-4 py-3 border-0 rounded-0 active-tab" 
                                id="dataUmumTab" onclick="switchTab('dataUmum')"
                                style="background: #74b9ff; color: white; border-radius: 15px 0 0 0 !important;">
                            <i class="fas fa-user me-2"></i>Data Umum
                        </button>
                        <button class="btn btn-link text-decoration-none px-4 py-3 border-0 rounded-0" 
                                id="jadwalTab" onclick="switchTab('jadwal')"
                                style="background: #ddd; color: #666;">
                            <i class="fas fa-calendar me-2"></i>Jadwal Mingguan
                        </button>
                    </div>

                    <form action="{{ url('/guru/'.$guru->id) }}" method="POST" id="guruForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Data Umum Tab Content -->
                        <div id="dataUmumContent" class="tab-content p-4">
                            <!-- Info Box -->
                            <div class="alert alert-info border-0 mb-4" style="background: #e3f2fd; border-radius: 10px;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Petunjuk:</strong> Perbarui data guru dan atur jadwal kerja untuk setiap hari dalam seminggu.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold text-dark mb-2">NIP</label>
                                    <input type="text" name="nip" class="form-control form-control-lg border-1 @error('nip') is-invalid @enderror" 
                                           style="border-radius: 8px; background: #f8f9fa;"
                                           value="{{ old('nip', $guru->nip) }}" required>
                                    @error('nip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold text-dark mb-2">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control form-control-lg border-1 @error('nama') is-invalid @enderror" 
                                           style="border-radius: 8px; background: #f8f9fa;"
                                           value="{{ old('nama', $guru->nama) }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold text-dark mb-2">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control form-control-lg border-1 @error('jabatan') is-invalid @enderror" 
                                           style="border-radius: 8px; background: #f8f9fa;"
                                           value="{{ old('jabatan', $guru->jabatan) }}" required>
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fas fa-credit-card text-warning me-2"></i>UID Kartu RFID
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" name="uid" id="uid" class="form-control form-control-lg border-1 @error('uid') is-invalid @enderror" 
                                               style="border-radius: 8px; background: #f8f9fa; font-family: monospace; padding-right: 50px;"
                                               value="{{ old('uid', $guru->uid) }}" required>
                                        <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                            <i class="fas fa-check-circle text-success fs-5" id="uid-check"></i>
                                        </div>
                                    </div>
                                    @error('uid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success mt-1 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="uid-status">UID berhasil terbaca: {{ $guru->uid }}</span>
                                    </small>
                                </div>

                                <!-- Bagian Registrasi Wajah (Face Recognition) -->
                                <div class="col-12 mb-4">
                                    <div class="card border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1">
                                                        <i class="fas fa-camera text-primary me-2"></i>Foto & Registrasi Wajah (Face Recognition)
                                                    </h6>
                                                    @if($guru->has_face)
                                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Wajah Sudah Terdaftar</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Wajah Belum Terdaftar</span>
                                                    @endif
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary active" id="btnModeUpload" onclick="setFaceMode('upload')">
                                                        <i class="fas fa-upload me-1"></i> Ganti Foto
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary" id="btnModeWebcam" onclick="setFaceMode('webcam')">
                                                        <i class="fas fa-video me-1"></i> Gunakan Kamera
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Hidden input untuk face descriptor -->
                                            <input type="hidden" name="face_descriptor" id="face_descriptor" value="{{ old('face_descriptor', $guru->face_descriptor) }}">

                                            <!-- Opsi Upload Foto -->
                                            <div id="sectionUpload">
                                                <label class="form-label text-muted small mb-1">Pilih foto wajah baru untuk memperbarui (kosongkan jika tidak diubah)</label>
                                                <input type="file" name="foto" id="fotoInput" class="form-control" accept="image/jpeg,image/png,image/jpg">
                                                @error('foto')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Opsi Webcam -->
                                            <div id="sectionWebcam" style="display: none;">
                                                <div class="text-center p-3 bg-dark rounded-3 position-relative" style="max-width: 400px; margin: 0 auto;">
                                                    <video id="webcamVideo" autoplay muted playsinline style="width: 100%; border-radius: 8px;"></video>
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-light fw-bold" id="btnCaptureWebcam" onclick="captureFromWebcam()">
                                                            <i class="fas fa-camera me-1 text-primary"></i> Ambil Foto Wajah
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preview & Deteksi AI -->
                                            <div id="facePreviewWrapper" class="mt-3 text-center" style="{{ $guru->foto_url ? '' : 'display: none;' }}">
                                                <p class="text-muted small mb-2 fw-semibold">Preview & Hasil Deteksi AI Wajah:</p>
                                                <div class="d-inline-block position-relative border rounded-3 p-1 bg-white shadow-sm" style="max-width: 240px;">
                                                    <img id="facePreviewImage" src="{{ $guru->foto_url ?? '' }}" alt="Foto Wajah Guru" class="img-fluid rounded" style="max-height: 220px; display: block;">
                                                    <canvas id="faceOverlayCanvas" style="position: absolute; top: 4px; left: 4px; pointer-events: none;"></canvas>
                                                </div>
                                            </div>

                                            <!-- Status Deteksi Wajah -->
                                            <div id="faceStatusBadge" class="mt-3 alert {{ $guru->has_face ? 'alert-success' : 'alert-secondary' }} py-2 px-3 border-0 d-flex align-items-center mb-0" style="border-radius: 8px; font-size: 0.88rem;">
                                                <i class="fas {{ $guru->has_face ? 'fa-check-circle text-success' : 'fa-info-circle text-secondary' }} me-2 fs-5" id="faceStatusIcon"></i>
                                                <span id="faceStatusText">
                                                    @if($guru->has_face)
                                                        Data fitur wajah telah tersimpan di sistem. Unggah foto baru atau gunakan kamera jika ingin memperbarui.
                                                    @else
                                                        Belum ada data wajah. Silakan unggah foto wajah atau gunakan kamera.
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jadwal Mingguan Tab Content -->
                        <div id="jadwalContent" class="tab-content p-4" style="display: none;">
                            <!-- Info Box -->
                            <div class="alert alert-info border-0 mb-4" style="background: #e3f2fd; border-radius: 10px;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Petunjuk:</strong> Atur jadwal kerja untuk setiap hari dalam seminggu. Hari yang diset "Libur" tidak akan dihitung dalam laporan absensi.
                            </div>

                            <!-- Schedule Table -->
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr style="background: #74b9ff; color: white;">
                                            <th class="py-3 px-4 fw-semibold" style="border-radius: 10px 0 0 10px;">Hari</th>
                                            <th class="py-3 px-4 fw-semibold text-center">Status</th>
                                            <th class="py-3 px-4 fw-semibold text-center">Jam Masuk</th>
                                            <th class="py-3 px-4 fw-semibold text-center">Jam Pulang</th>
                                            <th class="py-3 px-4 fw-semibold text-center" style="border-radius: 0 10px 10px 0;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                            $dayKeys = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
                                            $schedules = $guru->weeklySchedules->keyBy('hari');
                                        @endphp
                                        @foreach($days as $index => $day)
                                        @php
                                            $dayKey = $dayKeys[$index];
                                            $schedule = $schedules->get($dayKey);
                                            $isActive = $schedule ? $schedule->is_active : false;
                                            $jamMasuk = $schedule && $schedule->jam_masuk ? date('H:i', strtotime($schedule->jam_masuk)) : '07:00';
                                            $jamPulang = $schedule && $schedule->jam_pulang ? date('H:i', strtotime($schedule->jam_pulang)) : ($day == 'Jumat' ? '11:30' : '15:30');
                                        @endphp
                                        <tr class="border-bottom">
                                            <td class="py-3 px-4 fw-semibold">{{ $day }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input schedule-toggle" type="checkbox" 
                                                           name="schedule[{{ $dayKey }}][active]" 
                                                           id="toggle{{ $day }}" 
                                                           {{ $isActive ? 'checked' : '' }}
                                                           onchange="toggleSchedule('{{ $day }}')">
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <input type="time" name="schedule[{{ $dayKey }}][jam_masuk]" 
                                                       class="form-control text-center border-0 time-input-{{ $day }}" 
                                                       value="{{ $jamMasuk }}" style="background: #f8f9fa; border-radius: 8px;"
                                                       {{ !$isActive ? 'disabled' : '' }}>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <input type="time" name="schedule[{{ $dayKey }}][jam_pulang]" 
                                                       class="form-control text-center border-0 time-input-{{ $day }}" 
                                                       value="{{ $jamPulang }}" 
                                                       style="background: #f8f9fa; border-radius: 8px;"
                                                       {{ !$isActive ? 'disabled' : '' }}>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="badge status-badge-{{ $day }}" 
                                                      style="background: {{ $isActive ? '#28a745' : '#6c757d' }}; padding: 8px 16px; border-radius: 20px;">
                                                    {{ $isActive ? 'Aktif' : 'Libur' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Info Box Bottom -->
                            <div class="alert alert-info border-0 mt-4" style="background: #e3f2fd; border-radius: 10px;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Catatan:</strong> Jika status diatur ke "Libur", guru tidak akan bisa melakukan absensi pada hari tersebut.
                            </div>
                        </div>

                        <!-- Updated action buttons to match design -->
                        <div class="d-flex gap-3 justify-content-between p-4 border-top">
                            <a href="{{ url('/guru') }}" class="btn btn-lg px-4 py-2 border-0 shadow-sm" 
                               style="background: #74b9ff; color: white; border-radius: 10px;">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-lg px-4 py-2 border-0 shadow-sm"
                                    style="background: #00b894; color: white; border-radius: 10px;">
                                <i class="fas fa-save me-2"></i>Update Data Guru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.getElementById('dataUmumContent').style.display = 'none';
    document.getElementById('jadwalContent').style.display = 'none';
    
    // Remove active class from all tabs
    document.getElementById('dataUmumTab').style.background = '#ddd';
    document.getElementById('dataUmumTab').style.color = '#666';
    document.getElementById('jadwalTab').style.background = '#ddd';
    document.getElementById('jadwalTab').style.color = '#666';
    
    // Show selected tab content and activate tab
    if (tabName === 'dataUmum') {
        document.getElementById('dataUmumContent').style.display = 'block';
        document.getElementById('dataUmumTab').style.background = '#74b9ff';
        document.getElementById('dataUmumTab').style.color = 'white';
    } else if (tabName === 'jadwal') {
        document.getElementById('jadwalContent').style.display = 'block';
        document.getElementById('jadwalTab').style.background = '#74b9ff';
        document.getElementById('jadwalTab').style.color = 'white';
    }
}

function toggleSchedule(day) {
    const toggle = document.getElementById('toggle' + day);
    const timeInputs = document.querySelectorAll('.time-input-' + day);
    const statusBadge = document.querySelector('.status-badge-' + day);
    
    if (toggle.checked) {
        timeInputs.forEach(input => input.disabled = false);
        statusBadge.textContent = 'Aktif';
        statusBadge.style.background = '#28a745';
    } else {
        timeInputs.forEach(input => input.disabled = true);
        statusBadge.textContent = 'Libur';
        statusBadge.style.background = '#6c757d';
    }
}
</script>

<!-- Script Face-API.js untuk Deteksi & Ekstraksi Wajah -->
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
let faceModelsLoaded = false;
let webcamStream = null;
const MODEL_URL = "{{ asset('models') }}";

async function loadFaceModels() {
    if (faceModelsLoaded) return true;
    updateFaceStatus('loading', 'Memuat modul AI Face Recognition...');
    try {
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        faceModelsLoaded = true;
        updateFaceStatus('info', 'Modul AI siap. Silakan upload atau jepret foto wajah.');
        return true;
    } catch (err) {
        console.error('Gagal memuat model face-api:', err);
        updateFaceStatus('danger', 'Gagal memuat model AI: ' + err.message);
        return false;
    }
}

function updateFaceStatus(type, message) {
    const badge = document.getElementById('faceStatusBadge');
    const icon = document.getElementById('faceStatusIcon');
    const text = document.getElementById('faceStatusText');

    badge.className = 'mt-3 alert py-2 px-3 border-0 d-flex align-items-center mb-0';
    if (type === 'loading') {
        badge.classList.add('alert-info');
        icon.className = 'fas fa-spinner fa-spin me-2 fs-5 text-info';
    } else if (type === 'success') {
        badge.classList.add('alert-success');
        icon.className = 'fas fa-check-circle me-2 fs-5 text-success';
    } else if (type === 'warning') {
        badge.classList.add('alert-warning');
        icon.className = 'fas fa-exclamation-triangle me-2 fs-5 text-warning';
    } else if (type === 'danger') {
        badge.classList.add('alert-danger');
        icon.className = 'fas fa-times-circle me-2 fs-5 text-danger';
    } else {
        badge.classList.add('alert-secondary');
        icon.className = 'fas fa-info-circle me-2 fs-5 text-secondary';
    }
    text.textContent = message;
}

function setFaceMode(mode) {
    const btnUpload = document.getElementById('btnModeUpload');
    const btnWebcam = document.getElementById('btnModeWebcam');
    const secUpload = document.getElementById('sectionUpload');
    const secWebcam = document.getElementById('sectionWebcam');

    if (mode === 'upload') {
        btnUpload.classList.add('active');
        btnWebcam.classList.remove('active');
        secUpload.style.display = 'block';
        secWebcam.style.display = 'none';
        stopWebcam();
    } else {
        btnWebcam.classList.add('active');
        btnUpload.classList.remove('active');
        secUpload.style.display = 'none';
        secWebcam.style.display = 'block';
        startWebcam();
    }
}

async function startWebcam() {
    try {
        const video = document.getElementById('webcamVideo');
        webcamStream = await navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
            audio: false
        });
        video.srcObject = webcamStream;
        await loadFaceModels();
    } catch (err) {
        console.error('Kamera gagal diakses:', err);
        updateFaceStatus('danger', 'Kamera tidak dapat diakses atau izin kamera ditolak.');
    }
}

function stopWebcam() {
    if (webcamStream) {
        webcamStream.getTracks().forEach(t => t.stop());
        webcamStream = null;
    }
}

async function captureFromWebcam() {
    const video = document.getElementById('webcamVideo');
    if (!video.videoWidth) return;

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(async (blob) => {
        if (!blob) return;
        const file = new File([blob], 'foto_wajah_' + Date.now() + '.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        const fotoInput = document.getElementById('fotoInput');
        fotoInput.files = dt.files;

        // Tampilkan preview
        const img = document.getElementById('facePreviewImage');
        img.src = canvas.toDataURL('image/jpeg');
        document.getElementById('facePreviewWrapper').style.display = 'block';

        img.onload = () => {
            processFaceDetection(img);
        };
    }, 'image/jpeg', 0.9);
}

const fotoInputElement = document.getElementById('fotoInput');
if (fotoInputElement) {
    fotoInputElement.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(evt) {
            const img = document.getElementById('facePreviewImage');
            img.src = evt.target.result;
            document.getElementById('facePreviewWrapper').style.display = 'block';
            img.onload = () => {
                processFaceDetection(img);
            };
        };
        reader.readAsDataURL(file);
    });
}

async function processFaceDetection(imageEl) {
    const loaded = await loadFaceModels();
    if (!loaded) return;

    updateFaceStatus('loading', 'Menganalisis wajah dengan AI...');
    try {
        const detection = await faceapi.detectSingleFace(imageEl)
            .withFaceLandmarks()
            .withFaceDescriptor();

        const canvas = document.getElementById('faceOverlayCanvas');
        canvas.width = imageEl.width;
        canvas.height = imageEl.height;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (detection) {
            const descriptorArray = Array.from(detection.descriptor);
            document.getElementById('face_descriptor').value = JSON.stringify(descriptorArray);

            const displaySize = { width: imageEl.width, height: imageEl.height };
            faceapi.matchDimensions(canvas, displaySize);
            const resized = faceapi.resizeResults(detection, displaySize);
            faceapi.draw.drawDetections(canvas, resized);
            faceapi.draw.drawFaceLandmarks(canvas, resized);

            updateFaceStatus('success', 'Wajah berhasil dikenali & fitur descriptor AI berhasil diekstrak!');
        } else {
            document.getElementById('face_descriptor').value = '';
            updateFaceStatus('warning', 'Wajah tidak terdeteksi! Gunakan foto yang jelas, tegak lurus, dan cukup cahaya.');
        }
    } catch (err) {
        console.error('Error saat deteksi wajah:', err);
        updateFaceStatus('danger', 'Gagal memproses wajah: ' + err.message);
    }
}

// Preload models saat halaman dibuka
document.addEventListener('DOMContentLoaded', () => {
    loadFaceModels();
});
</script>

<style>
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input {
    width: 3rem;
    height: 1.5rem;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
