@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="card mb-4 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="color: #64b6f7; font-weight: 700; font-size: 2rem; font-family: 'DM Sans', sans-serif;">
                        <i class="fas fa-list me-3"></i>Daftar Presensi
                    </h1>
                    <p class="text-muted mb-0" style="font-family: 'DM Sans', sans-serif; color: #6b7280;">
                        Kelola dan edit keterangan Presensi guru & staff
                    </p>
                </div>
                <!-- Removed Input Manual and Dashboard buttons from header -->
            </div>
        </div>
    </div>

    <!-- Filter dan Search -->
    <div class="card mb-4 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
        <div class="card-body">
            <form method="GET" action="{{ url('/absensi/list') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">Tanggal Dari:</label>
                        <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari', date('Y-m-01')) }}" style="border: 2px solid #e5e7eb; border-radius: 8px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">Tanggal Sampai:</label>
                        <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai', date('Y-m-d')) }}" style="border: 2px solid #e5e7eb; border-radius: 8px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">Status:</label>
                        <select name="status" class="form-select" style="border: 2px solid #e5e7eb; border-radius: 8px;">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpa" {{ request('status') == 'alpa' ? 'selected' : '' }}>Alpa</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100" style="background: #64b6f7; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4" style="border: none; border-radius: 8px; background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2" style="color: #10b981;"></i>
            <span style="color: #047857; font-family: 'DM Sans', sans-serif;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabel Absensi -->
    <div class="card shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                        <tr>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem;">
                                <i class="fas fa-calendar me-2" style="color: #64b6f7;"></i>Tanggal
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem;">
                                <i class="fas fa-user me-2" style="color: #10b981;"></i>Nama
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem;">
                                <i class="fas fa-id-badge me-2" style="color: #6366f1;"></i>NIP
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem;">
                                <i class="fas fa-info-circle me-2" style="color: #f59e0b;"></i>Status
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem;">
                                <i class="fas fa-clock me-2" style="color: #8b5cf6;"></i>Jam
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem;">
                                <i class="fas fa-comment me-2" style="color: #64b6f7;"></i>Keterangan
                            </th>
                            <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #374151; font-size: 0.875rem;">
                                <i class="fas fa-cogs me-2" style="color: #64b6f7;"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $item)
                            <tr style="transition: all 0.2s ease; border-bottom: 1px solid #f3f4f6;">
                                <td class="px-4 py-3 align-middle">
                                    <div style="font-family: 'DM Sans', sans-serif; color: #374151; font-weight: 500;">
                                        @if($item->tanggal instanceof \Carbon\Carbon)
                                            {{ $item->tanggal->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $item->tanggal->format('l') }}</small>
                                        @else
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal)->format('l') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3" style="width: 35px; height: 35px; background: #64b6f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;">
                                            {{ strtoupper(substr($item->guru->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-family: 'DM Sans', sans-serif; color: #374151; font-weight: 500;">{{ $item->guru->nama }}</div>
                                            <small class="text-muted">{{ $item->guru->jabatan }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div style="font-family: 'DM Sans', sans-serif; color: #374151; font-weight: 500;">
                                        {{ $item->guru->nip ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <span class="badge {{ $item->status_badge_class }}" style="padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500;">
                                        {{ $item->status_masuk_label }}
                                    </span>
                                    @if($item->is_manual)
                                        <br><small class="badge bg-secondary mt-1" style="font-size: 0.6rem;">Manual</small>
                                    @endif
                                    @if($item->status_verifikasi === 'terverifikasi')
                                        <br><span class="badge mt-1" style="background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.65rem; border-radius: 12px;" title="{{ $item->catatan_verifikasi }}">
                                            <i class="fas fa-check-circle me-1"></i>Wajah Terverifikasi
                                        </span>
                                    @elseif($item->status_verifikasi === 'gagal')
                                        <br><span class="badge mt-1" style="background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.65rem; border-radius: 12px;" title="{{ $item->catatan_verifikasi }}">
                                            <i class="fas fa-times-circle me-1"></i>Wajah Tidak Cocok
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div style="font-family: 'DM Sans', sans-serif; color: #374151;">
                                        @if($item->jam_masuk)
                                            <div><i class="fas fa-sign-in-alt me-1 text-success"></i>
                                                @if($item->jam_masuk instanceof \Carbon\Carbon)
                                                    {{ $item->jam_masuk->format('H:i') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                                                @endif
                                            </div>
                                        @endif
                                        @if($item->jam_keluar)
                                            <div><i class="fas fa-sign-out-alt me-1 text-danger"></i>
                                                @if($item->jam_keluar instanceof \Carbon\Carbon)
                                                    {{ $item->jam_keluar->format('H:i') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div style="max-width: 200px;">
                                        @if($item->keterangan_masuk)
                                            <div class="mb-1">
                                                <small class="text-muted">Masuk:</small>
                                                <div style="font-size: 0.85rem; color: #374151;">{{ Str::limit($item->keterangan_masuk, 50) }}</div>
                                            </div>
                                        @endif
                                        @if($item->keterangan_keluar)
                                            <div>
                                                <small class="text-muted">Keluar:</small>
                                                <div style="font-size: 0.85rem; color: #374151;">{{ Str::limit($item->keterangan_keluar, 50) }}</div>
                                            </div>
                                        @endif
                                        @if(!$item->keterangan_masuk && !$item->keterangan_keluar)
                                            <span class="text-muted" style="font-style: italic;">Tidak ada keterangan</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('absensi.edit.keterangan', $item->id) }}" 
                                           class="btn btn-sm {{ $item->canEdit() ? 'btn-warning' : 'btn-outline-secondary' }}" 
                                           style="border-radius: 6px; margin-right: 5px; font-family: 'DM Sans', sans-serif; font-weight: 500;"
                                           {{ !$item->canEdit() ? 'disabled title="Tidak dapat diedit (lewat 24 jam)"' : '' }}>
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        
                                        <!-- Adding evidence view button for manual attendance -->
                                        @if($item->is_manual && $item->hasEvidence())
                                            <button type="button" 
                                                    class="btn btn-sm btn-info me-1" 
                                                    style="border-radius: 6px; font-family: 'DM Sans', sans-serif; font-weight: 500;"
                                                    onclick="viewEvidence({{ $item->id }})">
                                                <i class="fas fa-paperclip me-1"></i>Lihat Bukti
                                            </button>
                                        @endif
                                        
                                        <!-- Update tombol lihat foto untuk menampilkan multiple photos -->
                                        @if($item->hasPhotos())
                                            <button type="button" 
                                                    class="btn btn-sm btn-success" 
                                                    style="border-radius: 6px; font-family: 'DM Sans', sans-serif; font-weight: 500;"
                                                    onclick="loadPhotos({{ $item->id }})">
                                                <i class="fas fa-camera me-1"></i>Lihat Foto
                                                @if($item->getPhotosCount() > 1)
                                                    <span class="badge bg-light text-dark ms-1">{{ $item->getPhotosCount() }}</span>
                                                @endif
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary" 
                                                    style="border-radius: 6px; font-family: 'DM Sans', sans-serif; font-weight: 500;"
                                                    disabled 
                                                    title="Foto tidak tersedia">
                                                <i class="fas fa-camera me-1"></i>Tidak Ada Foto
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div style="color: #6b7280; font-family: 'DM Sans', sans-serif;">
                                        <i class="fas fa-inbox fa-3x mb-3" style="color: #d1d5db;"></i>
                                        <h5>Tidak ada data absensi</h5>
                                        <p>Belum ada data absensi untuk periode yang dipilih</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($absensi->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $absensi->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Photo Viewer -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e5e7eb; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="photoModalLabel" style="color: #374151; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                    <i class="fas fa-camera me-2" style="color: #64b6f7;"></i>Foto Absensi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 text-center">
                    <h6 id="photoPersonName" style="color: #374151; font-family: 'DM Sans', sans-serif; font-weight: 600;"></h6>
                    <small id="photoDateTime" class="text-muted" style="font-family: 'DM Sans', sans-serif;"></small>
                </div>
                
                <!-- Loading indicator -->
                <div id="photoLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat foto...</p>
                </div>
                
                <!-- Photos container -->
                <div id="photosContainer" style="display: none;">
                    <div id="photoCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner" id="carouselInner">
                            <!-- Photos will be loaded here -->
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev" style="display: none;" id="prevBtn">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next" style="display: none;" id="nextBtn">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    
                    <!-- Photo indicators -->
                    <div class="mt-3 text-center" id="photoIndicators">
                        <!-- Indicators will be loaded here -->
                    </div>
                </div>
                
                <!-- Error message -->
                <div id="photoError" class="text-center py-5" style="display: none;">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6>Tidak dapat memuat foto</h6>
                    <p class="text-muted">Terjadi kesalahan saat memuat foto absensi</p>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e5e7eb; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 500;">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Adding Evidence Modal -->
<div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e5e7eb; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="evidenceModalLabel" style="color: #374151; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                    <i class="fas fa-paperclip me-2" style="color: #10b981;"></i>Bukti Pendukung Absensi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 text-center">
                    <h6 id="evidencePersonName" style="color: #374151; font-family: 'DM Sans', sans-serif; font-weight: 600;"></h6>
                    <small id="evidenceDateTime" class="text-muted" style="font-family: 'DM Sans', sans-serif;"></small>
                </div>
                
                <!-- Loading indicator -->
                <div id="evidenceLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat bukti...</p>
                </div>
                
                <!-- Evidence container -->
                <div id="evidenceContainer" style="display: none;" class="text-center">
                    <div id="evidenceContent">
                        <!-- Evidence will be loaded here -->
                    </div>
                </div>
                
                <!-- Error message -->
                <div id="evidenceError" class="text-center py-5" style="display: none;">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6>Tidak dapat memuat bukti</h6>
                    <p class="text-muted">Terjadi kesalahan saat memuat bukti pendukung</p>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e5e7eb; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 500;">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <a id="downloadEvidenceBtn" href="#" class="btn btn-primary" target="_blank" style="border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 500; display: none;">
                    <i class="fas fa-download me-1"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(100, 182, 247, 0.05) !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(100, 182, 247, 0.1);
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.avatar-circle {
    transition: all 0.2s ease;
}

.avatar-circle:hover {
    transform: scale(1.05);
}

.form-control:focus, .form-select:focus {
    border-color: #64b6f7;
    box-shadow: 0 0 0 0.2rem rgba(100, 182, 247, 0.25);
    outline: none;
}

/* Menambahkan style untuk modal foto */
.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.photo-container img {
    transition: transform 0.3s ease;
}

.photo-container img:hover {
    transform: scale(1.02);
}

/* Memperbaiki CSS carousel untuk menghindari overlap slide */
.carousel-inner {
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    width: 100%;
    height: 500px;
}

.carousel-item {
    position: relative;
    display: none;
    float: left;
    width: 100%;
    height: 100%;
    margin-right: -100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    transition: transform 0.6s ease-in-out;
}

.carousel-item.active,
.carousel-item-next,
.carousel-item-prev {
    display: block;
}

.carousel-item-next:not(.carousel-item-start),
.active.carousel-item-end {
    transform: translateX(100%);
}

.carousel-item-prev:not(.carousel-item-end),
.active.carousel-item-start {
    transform: translateX(-100%);
}

.carousel-item .text-center {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.carousel-item img {
    max-width: 100%;
    max-height: 400px;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;
    border-radius: 12px !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

#photosContainer {
    min-height: 500px;
}

/* Menambahkan style untuk carousel controls */
.carousel-control-prev,
.carousel-control-next {
    position: absolute;
    top: 0;
    bottom: 0;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 15%;
    padding: 0;
    color: #fff;
    text-align: center;
    background: 0 0;
    border: 0;
    opacity: 0.5;
    transition: opacity 0.15s ease;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 0.9;
}

.carousel-control-prev {
    left: 0;
}

.carousel-control-next {
    right: 0;
}
</style>

<!-- Menambahkan JavaScript untuk handle multiple photos -->
<script>
let currentPhotos = [];
let currentPhotoIndex = 0;
let carouselInstance = null;

function loadPhotos(absensiId) {
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('photoModal'));
    modal.show();
    
    // Show loading
    document.getElementById('photoLoading').style.display = 'block';
    document.getElementById('photosContainer').style.display = 'none';
    document.getElementById('photoError').style.display = 'none';
    
    // Fetch photos
    fetch(`/absensi/${absensiId}/photos`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentPhotos = data.data.photos;
                displayPhotos(data.data);
            } else {
                showError();
            }
        })
        .catch(error => {
            console.error('Error loading photos:', error);
            showError();
        });
}

function displayPhotos(data) {
    document.getElementById('photoLoading').style.display = 'none';
    
    if (currentPhotos.length === 0) {
        showError('Tidak ada foto tersedia');
        return;
    }
    
    const tanggalFormatted = formatTanggal(data.absensi.tanggal);
    
    // Update modal title
    document.getElementById('photoPersonName').textContent = data.guru.nama;
    document.getElementById('photoDateTime').textContent = `Tanggal: ${tanggalFormatted}`;
    
    // Build carousel
    const carouselInner = document.getElementById('carouselInner');
    const photoIndicators = document.getElementById('photoIndicators');
    
    carouselInner.innerHTML = '';
    photoIndicators.innerHTML = '';
    
    currentPhotos.forEach((photo, index) => {
        // Create carousel item
        const carouselItem = document.createElement('div');
        carouselItem.className = `carousel-item ${index === 0 ? 'active' : ''}`;
        carouselItem.innerHTML = ` 
            <div class="text-center w-100">
                <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
                    <img src="${photo.photo_url}" 
                         alt="Foto Absensi" 
                         class="img-fluid" 
                         style="max-height: 450px; max-width: 100%; object-fit: contain;"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMDAgMTUwQzIwNSAxNTAgMjEwIDE0NSAyMTAgMTQwQzIxMCAxMzUgMjA1IDEzMCAyMDAgMTMwQzE5NSAxMzAgMTkwIDEzNSAxOTAgMTQwQzE5MCAxNDUgMTk1IDE1MCAyMDAgMTUwWiIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMTcwIDE4MEwyMzAgMTgwTDIyMCAxNjBMMjAwIDE3MEwxODAgMTYwTDE3MCAxODBaIiBmaWxsPSIjOUNBM0FGIi8+Cjx0ZXh0IHg9IjIwMCIgeT0iMjIwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNkI3MjgwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiPkZvdG8gdGlkYWsgZGFwYXQgZGltdWF0PC90ZXh0Pgo8L3N2Zz4K';">
                </div>
                <div class="mt-4 text-center" style="padding: 15px; background: #f8fafc; border-radius: 8px; margin-top: 20px;">
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">
                            <i class="fas fa-clock me-1"></i>Waktu Capture
                        </small>
                        <span style="font-weight: 500; color: #374151;">${formatWaktu(photo.captured_at)}</span>
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-${getTypeColor(photo.capture_type)} px-3 py-2" style="font-size: 0.8rem;">
                            ${getTypeLabel(photo.capture_type)}
                        </span>
                    </div>
                    ${photo.capture_context ? `<div class="mt-2"><small class="text-muted" style="font-style: italic;">${photo.capture_context}</small></div>` : ''}
                </div>
            </div>
        `;
        carouselInner.appendChild(carouselItem);
        
        // Create indicator
        const indicator = document.createElement('button');
        indicator.className = `btn btn-sm ${index === 0 ? 'btn-primary' : 'btn-outline-primary'} me-1`;
        indicator.textContent = index + 1;
        indicator.onclick = () => goToPhoto(index);
        photoIndicators.appendChild(indicator);
    });

    // Dispose existing carousel instance if exists
    if (carouselInstance) {
        carouselInstance.dispose();
    }
    
    // Initialize new carousel instance
    const carouselElement = document.getElementById('photoCarousel');
    carouselInstance = new bootstrap.Carousel(carouselElement, {
        interval: false, // Disable auto-slide
        wrap: true,
        touch: true
    });
    
    carouselElement.addEventListener('slid.bs.carousel', function (event) {
        const activeIndex = event.to;
        updateIndicators(activeIndex);
        currentPhotoIndex = activeIndex;
    });

    // Show/hide navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (currentPhotos.length > 1) {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    } else {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
    }
    
    currentPhotoIndex = 0;
    updateIndicators(0);
    
    document.getElementById('photosContainer').style.display = 'block';
}

function goToPhoto(index) {
    if (!carouselInstance || index < 0 || index >= currentPhotos.length) {
        return;
    }
    
    // Use carousel instance to slide to specific index
    carouselInstance.to(index);
    
    // Update indicators immediately
    updateIndicators(index);
    currentPhotoIndex = index;
}

function updateIndicators(activeIndex) {
    const indicators = document.querySelectorAll('#photoIndicators button');
    indicators.forEach((btn, i) => {
        btn.className = `btn btn-sm ${i === activeIndex ? 'btn-primary' : 'btn-outline-primary'} me-1`;
    });
}

function getTypeColor(type) {
    switch(type) {
        case 'masuk': return 'success';
        case 'keluar': return 'info';
        case 'failed_attempt': return 'warning';
        default: return 'secondary';
    }
}

function getTypeLabel(type) {
    switch(type) {
        case 'masuk': return 'Absen Masuk';
        case 'keluar': return 'Absen Keluar';
        case 'failed_attempt': return 'Percobaan Gagal';
        default: return 'Lainnya';
    }
}

function showError(message = 'Tidak dapat memuat foto') {
    document.getElementById('photoLoading').style.display = 'none';
    document.getElementById('photosContainer').style.display = 'none';
    document.getElementById('photoError').style.display = 'block';
    document.getElementById('photoError').querySelector('p').textContent = message;
}

// Legacy function for backward compatibility
function showPhoto(photoUrl, personName, dateTime) {
    // This function is kept for any remaining single photo displays
    loadPhotos(0); // This won't work, but prevents JS errors
}

function formatTanggal(tanggalString) {
    try {
        const date = new Date(tanggalString);
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            weekday: 'long'
        };
        return date.toLocaleDateString('id-ID', options);
    } catch (error) {
        return tanggalString; // fallback ke string asli jika error
    }
}

function formatWaktu(waktuString) {
    try {
        if (!waktuString || waktuString === 'null' || waktuString === 'undefined') {
            return 'Waktu tidak tersedia';
        }
        
        const date = new Date(waktuString);
        
        // Validasi apakah date valid
        if (isNaN(date.getTime())) {
            // Coba parse format lain (DD/MM/YYYY HH:MM:SS)
            const parts = waktuString.split(' ');
            if (parts.length >= 1) {
                const dateParts = parts[0].split('/');
                if (dateParts.length === 3) {
                    const newDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                    if (!isNaN(newDate.getTime())) {
                        return waktuString; // Return original jika valid
                    }
                }
            }
            return 'Waktu tidak valid';
        }
        
        const options = { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        return date.toLocaleDateString('id-ID', options);
    } catch (error) {
        console.error('Error formatting time:', error);
        return waktuString || 'Waktu tidak tersedia';
    }
}

document.getElementById('photoModal').addEventListener('hidden.bs.modal', function () {
    if (carouselInstance) {
        carouselInstance.dispose();
        carouselInstance = null;
    }
    currentPhotos = [];
    currentPhotoIndex = 0;
});

function viewEvidence(absensiId) {
    window.currentEvidenceId = absensiId;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('evidenceModal'));
    modal.show();
    
    // Show loading
    document.getElementById('evidenceLoading').style.display = 'block';
    document.getElementById('evidenceContainer').style.display = 'none';
    document.getElementById('evidenceError').style.display = 'none';
    document.getElementById('downloadEvidenceBtn').style.display = 'none';
    
    // Fetch evidence
    fetch(`/absensi/${absensiId}/evidence`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.data.absensi_id = absensiId;
                displayEvidence(data.data);
            } else {
                showEvidenceError();
            }
        })
        .catch(error => {
            console.error('Error loading evidence:', error);
            showEvidenceError();
        });
}

function displayEvidence(data) {
    document.getElementById('evidenceLoading').style.display = 'none';
    
    // Update modal title
    document.getElementById('evidencePersonName').textContent = data.guru_nama;
    document.getElementById('evidenceDateTime').textContent = `Tanggal: ${data.tanggal} - Status: ${data.status}`;
    
    // Display evidence
    const evidenceContent = document.getElementById('evidenceContent');
    const fileExtension = data.evidence_url.split('.').pop().toLowerCase();
    
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        // Display image
        evidenceContent.innerHTML = ` 
            <img src="${data.evidence_url}" 
                 alt="Bukti Pendukung" 
                 class="img-fluid" 
                 style="max-height: 400px; max-width: 100%; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMDAgMTUwQzIwNSAxNTAgMjEwIDE0NSAyMTAgMTQwQzIxMCAxMzUgMjA1IDEzMCAyMDAgMTMwQzE5NSAxMzAgMTkwIDEzNSAxOTAgMTQwQzE5MCAxNDUgMTk1IDE1MCAyMDAgMTUwWiIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMTcwIDE4MEwyMzAgMTgwTDIyMCAxNjBMMjAwIDE3MEwxODAgMTYwTDE3MCAxODBaIiBmaWxsPSIjOUNBM0FGIi8+Cjx0ZXh0IHg9IjIwMCIgeT0iMjIwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNkI3MjgwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiPkZvdG8gdGlkYWsgZGFwYXQgZGltdWF0PC90ZXh0Pgo8L3N2Zz4K';">
        `;
    } else {
        // Display file icon for documents
        const fileIcon = getFileIcon(fileExtension);
        evidenceContent.innerHTML = ` 
            <div class="text-center py-4">
                <i class="${fileIcon} fa-5x mb-3" style="color: #64b6f7;"></i>
                <h6>Dokumen Bukti Pendukung</h6>
                <p class="text-muted">Klik tombol download untuk melihat dokumen</p>
            </div>
        `;
    }
    
    const downloadBtn = document.getElementById('downloadEvidenceBtn');
    // Extract absensi ID from the evidence URL or use a data attribute
    const absensiId = data.absensi_id || window.currentEvidenceId;
    downloadBtn.href = `/absensi/${absensiId}/download-evidence`;
    downloadBtn.style.display = 'inline-block';
    
    document.getElementById('evidenceContainer').style.display = 'block';
}

function getFileIcon(extension) {
    switch(extension) {
        case 'pdf': return 'fas fa-file-pdf';
        case 'doc':
        case 'docx': return 'fas fa-file-word';
        default: return 'fas fa-file';
    }
}

function showEvidenceError() {
    document.getElementById('evidenceLoading').style.display = 'none';
    document.getElementById('evidenceContainer').style.display = 'none';
    document.getElementById('evidenceError').style.display = 'block';
}
</script>
@endsection
