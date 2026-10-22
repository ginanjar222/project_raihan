@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="card mb-4 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="color: #64b6f7; font-weight: 700; font-size: 2rem; font-family: 'DM Sans', sans-serif;">
                        <i class="fas fa-edit me-3"></i>Edit Keterangan Presensi
                    </h1>
                    <p class="text-muted mb-0" style="font-family: 'DM Sans', sans-serif; color: #6b7280;">
                        Edit keterangan untuk {{ $absensi->guru->nama }} - {{ $absensi->tanggal->format('d F Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ url('/absensi/list') }}" class="btn btn-secondary" style="background: #6b7280; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4" style="border: none; border-radius: 8px; background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2" style="color: #10b981;"></i>
            <span style="color: #047857; font-family: 'DM Sans', sans-serif;">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm mb-4" style="border: none; border-radius: 8px; background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-circle me-2" style="color: #ef4444;"></i>
            <div style="color: #dc2626; font-family: 'DM Sans', sans-serif;">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Info Absensi -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                <div class="card-body">
                    <h5 class="card-title mb-3" style="color: #374151; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                        <i class="fas fa-info-circle me-2" style="color: #64b6f7;"></i>Informasi Presensi
                    </h5>
                    
                    <div class="info-item mb-3">
                        <label class="fw-semibold" style="color: #6b7280; font-size: 0.85rem;">Nama:</label>
                        <div style="color: #374151; font-weight: 500;">{{ $absensi->guru->nama }}</div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="fw-semibold" style="color: #6b7280; font-size: 0.85rem;">Jabatan:</label>
                        <div style="color: #374151; font-weight: 500;">{{ $absensi->guru->jabatan }}</div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="fw-semibold" style="color: #6b7280; font-size: 0.85rem;">Tanggal:</label>
                        <div style="color: #374151; font-weight: 500;">{{ $absensi->tanggal->format('d F Y') }}</div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="fw-semibold" style="color: #6b7280; font-size: 0.85rem;">Status:</label>
                        <div>
                            <span class="badge {{ $absensi->status_badge_class }}" style="padding: 6px 12px; border-radius: 20px; font-size: 0.75rem;">
                                {{ $absensi->status_masuk_label }}
                            </span>
                        </div>
                    </div>
                    
                    @if($absensi->jam_masuk)
                    <div class="info-item mb-3">
                        <label class="fw-semibold" style="color: #6b7280; font-size: 0.85rem;">Jam Masuk:</label>
                        <div style="color: #374151; font-weight: 500;">{{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}</div>
                    </div>
                    @endif
                    
                    @if($absensi->jam_keluar)
                    <div class="info-item mb-3">
                        <label class="fw-semibold" style="color: #6b7280; font-size: 0.85rem;">Jam Keluar:</label>
                        <div style="color: #374151; font-weight: 500;">{{ \Carbon\Carbon::parse($absensi->jam_keluar)->format('H:i') }}</div>
                    </div>
                    @endif
                    
                    <div class="info-item">
                        <label class="fw-semibold" style="color: #6b7280; font-size: 0.85rem;">Tipe Input:</label>
                        <div>
                            <span class="badge" style="background: {{ $absensi->is_manual ? 'rgba(139, 92, 246, 0.1)' : 'rgba(34, 197, 94, 0.1)' }}; color: {{ $absensi->is_manual ? '#8b5cf6' : '#22c55e' }}; border: 1px solid {{ $absensi->is_manual ? 'rgba(139, 92, 246, 0.2)' : 'rgba(34, 197, 94, 0.2)' }}; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem;">
                                {{ $absensi->is_manual ? 'Manual' : 'Scan Kartu' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit Keterangan -->
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                <div class="card-body">
                    <h5 class="card-title mb-4" style="color: #374151; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                        <i class="fas fa-comment-edit me-2" style="color: #f59e0b;"></i>Edit Keterangan
                    </h5>
                    
                    <form action="{{ route('absensi.update.keterangan', $absensi->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Keterangan Masuk -->
                        <div class="mb-4">
                            <label for="keterangan_masuk" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-sign-in-alt me-2" style="color: #10b981;"></i>Keterangan Masuk
                            </label>
                            <textarea class="form-control" id="keterangan_masuk" name="keterangan_masuk" rows="4" 
                                      placeholder="Masukkan keterangan untuk jam masuk (opsional)"
                                      style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">{{ old('keterangan_masuk', $absensi->keterangan_masuk) }}</textarea>
                            <small class="text-muted" style="font-family: 'DM Sans', sans-serif;">
                                Contoh: Terlambat karena macet, Izin keperluan keluarga, Sakit demam, dll.
                            </small>
                        </div>

                        <!-- Keterangan Keluar -->
                        <div class="mb-4">
                            <label for="keterangan_keluar" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-sign-out-alt me-2" style="color: #ef4444;"></i>Keterangan Keluar
                            </label>
                            <textarea class="form-control" id="keterangan_keluar" name="keterangan_keluar" rows="4" 
                                      placeholder="Masukkan keterangan untuk jam keluar (opsional)"
                                      style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">{{ old('keterangan_keluar', $absensi->keterangan_keluar) }}</textarea>
                            <small class="text-muted" style="font-family: 'DM Sans', sans-serif;">
                                Contoh: Pulang lebih awal karena sakit, Lembur sampai malam, dll.
                            </small>
                        </div>

                        <!-- Info Waktu Edit -->
                        @if(!$absensi->canEdit())
                        <div class="alert alert-warning mb-4" style="border: none; border-radius: 8px; background: rgba(217, 119, 6, 0.1); border-left: 4px solid #d97706;">
                            <i class="fas fa-exclamation-triangle me-2" style="color: #d97706;"></i>
                            <span style="color: #92400e; font-family: 'DM Sans', sans-serif;">
                                <strong>Peringatan:</strong> Data ini sudah melewati batas waktu edit (24 jam). Perubahan mungkin tidak dapat disimpan.
                            </span>
                        </div>
                        @else
                        <div class="alert alert-info mb-4" style="border: none; border-radius: 8px; background: rgba(100, 182, 247, 0.1); border-left: 4px solid #64b6f7;">
                            <i class="fas fa-info-circle me-2" style="color: #64b6f7;"></i>
                            <span style="color: #1e40af; font-family: 'DM Sans', sans-serif;">
                                <strong>Info:</strong> Data ini dapat diedit karena masih dalam batas waktu 24 jam atau merupakan input manual.
                            </span>
                        </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ url('/absensi/list') }}" class="btn btn-outline-secondary" style="border: 2px solid #6b7280; color: #6b7280; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" style="background: #64b6f7; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600;" {{ !$absensi->canEdit() ? 'disabled' : '' }}>
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    border-color: #64b6f7;
    box-shadow: 0 0 0 0.2rem rgba(100, 182, 247, 0.25);
    outline: none;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-primary:hover {
    background: #007acc !important;
}

.btn-outline-secondary:hover {
    background: #6b7280;
    color: white;
}

.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.info-item:last-child {
    border-bottom: none;
}

.alert {
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
