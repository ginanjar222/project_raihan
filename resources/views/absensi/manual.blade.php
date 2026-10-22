@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="card mb-4 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="color: #64b6f7; font-weight: 700; font-size: 2rem; font-family: 'DM Sans', sans-serif;">
                        <i class="fas fa-edit me-3"></i>Input Manual Presensi
                    </h1>
                    <p class="text-muted mb-0" style="font-family: 'DM Sans', sans-serif; color: #6b7280;">
                        Input Presensi manual untuk izin, sakit, atau alpa
                    </p>
                </div>
                <!-- Removed back button from header -->
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

    <!-- Form Input Manual -->
    <div class="card shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
        <div class="card-body p-4">
            <form action="{{ route('absensi.manual.store') }}" method="POST" id="manualAbsensiForm" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Pilih Guru -->
                    <div class="col-md-6 mb-4">
                        <label for="guru_id" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                            <i class="fas fa-user me-2" style="color: #64b6f7;"></i>Pilih Guru/Staff
                        </label>
                        <select class="form-select" id="guru_id" name="guru_id" required style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                            <option value="">-- Pilih Guru/Staff --</option>
                            @foreach($guru as $g)
                                <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }} - {{ $g->jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div class="col-md-6 mb-4">
                        <label for="tanggal" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                            <i class="fas fa-calendar me-2" style="color: #10b981;"></i>Tanggal
                        </label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" 
                               value="{{ old('tanggal', date('Y-m-d')) }}" required
                               style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                    </div>
                </div>

                <div class="row">
                    <!-- Status Kehadiran -->
                    <div class="col-md-6 mb-4">
                        <label for="status_masuk" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                            <i class="fas fa-info-circle me-2" style="color: #f59e0b;"></i>Status Kehadiran
                        </label>
                        <select class="form-select" id="status_masuk" name="status_masuk" required 
                                style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                            <option value="">-- Pilih Status --</option>
                            <option value="izin" {{ old('status_masuk') == 'izin' ? 'selected' : '' }}>
                                <i class="fas fa-info-circle"></i> Izin
                            </option>
                            <option value="sakit" {{ old('status_masuk') == 'sakit' ? 'selected' : '' }}>
                                <i class="fas fa-thermometer-half"></i> Sakit
                            </option>
                            <option value="alpa" {{ old('status_masuk') == 'alpa' ? 'selected' : '' }}>
                                <i class="fas fa-times-circle"></i> Alpa (Tidak Hadir)
                            </option>
                        </select>
                    </div>

                    <!-- Jam (Optional untuk izin/sakit) -->
                    <div class="col-md-6 mb-4">
                        <label for="jam_masuk" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                            <i class="fas fa-clock me-2" style="color: #64b6f7;"></i>Jam Masuk <small class="text-muted">(Opsional)</small>
                        </label>
                        <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" 
                               value="{{ old('jam_masuk') }}"
                               style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                        <small class="text-muted" style="font-family: 'DM Sans', sans-serif;">
                            Kosongkan jika tidak hadir sama sekali
                        </small>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-4">
                    <label for="keterangan_masuk" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                        <i class="fas fa-comment me-2" style="color: #8b5cf6;"></i>Keterangan
                    </label>
                    <textarea class="form-control" id="keterangan_masuk" name="keterangan_masuk" rows="4" 
                              placeholder="Masukkan keterangan detail (contoh: sakit demam, izin keperluan keluarga, dll.)"
                              style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">{{ old('keterangan_masuk') }}</textarea>
                </div>

                <!-- Bukti Pendukung -->
                <div class="mb-4">
                    <label for="evidence_file" class="form-label fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                        <i class="fas fa-paperclip me-2" style="color: #10b981;"></i>Bukti Pendukung <small class="text-muted">(Opsional)</small>
                    </label>
                    <input type="file" class="form-control" id="evidence_file" name="evidence_file" 
                           accept="image/*,.pdf,.doc,.docx" 
                           style="border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                    <small class="text-muted" style="font-family: 'DM Sans', sans-serif;">
                        Upload foto atau dokumen pendukung (maksimal 2MB). Format: JPG, PNG, PDF, DOC, DOCX
                    </small>
                    <div id="file-preview" class="mt-2" style="display: none;">
                        <div class="alert alert-info" style="border-radius: 8px; background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981;">
                            <i class="fas fa-file me-2" style="color: #10b981;"></i>
                            <span id="file-name" style="color: #047857; font-family: 'DM Sans', sans-serif;"></span>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Status -->
                <div class="alert alert-info mb-4" style="border: none; border-radius: 8px; background: rgba(100, 182, 247, 0.1); border-left: 4px solid #64b6f7;">
                    <div style="color: #1e40af; font-family: 'DM Sans', sans-serif;">
                        <strong><i class="fas fa-info-circle me-2"></i>Informasi Status:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Izin:</strong> Tidak hadir dengan pemberitahuan sebelumnya</li>
                            <li><strong>Sakit:</strong> Tidak hadir karena kondisi kesehatan</li>
                            <li><strong>Alpa:</strong> Tidak hadir tanpa keterangan yang jelas</li>
                        </ul>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-3">
                    <button type="reset" class="btn btn-outline-secondary" style="border: 2px solid #6b7280; color: #6b7280; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg" style="background: #64b6f7; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                        <i class="fas fa-save me-2"></i>Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                <div class="modal-header" style="border: none; padding: 2rem 2rem 1rem 2rem; background: linear-gradient(135deg, #64b6f7 0%, #007acc 100%); border-radius: 16px 16px 0 0;">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="color: white; font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="confirmModalLabel" style="color: white; font-family: 'DM Sans', sans-serif; font-weight: 700; font-size: 1.25rem;">
                                Konfirmasi Input Absensi
                            </h5>
                            <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 0.875rem; font-family: 'DM Sans', sans-serif;">
                                Pastikan data yang diinput sudah benar
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="confirmation-details">
                        <div class="detail-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3" style="width: 40px; height: 40px; background: rgba(100, 182, 247, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="color: #64b6f7; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted" style="font-family: 'DM Sans', sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Guru/Staff</small>
                                    <div id="confirm-guru" style="font-family: 'DM Sans', sans-serif; font-weight: 600; color: #374151;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-calendar" style="color: #10b981; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted" style="font-family: 'DM Sans', sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal</small>
                                    <div id="confirm-tanggal" style="font-family: 'DM Sans', sans-serif; font-weight: 600; color: #374151;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3" style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i id="confirm-status-icon" class="fas fa-info-circle" style="color: #f59e0b; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted" style="font-family: 'DM Sans', sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</small>
                                    <div id="confirm-status" style="font-family: 'DM Sans', sans-serif; font-weight: 600; color: #374151;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item" id="confirm-jam-wrapper" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3" style="width: 40px; height: 40px; background: rgba(100, 182, 247, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-clock" style="color: #64b6f7; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted" style="font-family: 'DM Sans', sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Jam Masuk</small>
                                    <div id="confirm-jam" style="font-family: 'DM Sans', sans-serif; color: #374151; margin-top: 0.25rem; line-height: 1.5;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item" id="confirm-file-wrapper" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-file" style="color: #10b981; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted" style="font-family: 'DM Sans', sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Bukti Pendukung</small>
                                    <div id="confirm-file" style="font-family: 'DM Sans', sans-serif; color: #374151; margin-top: 0.25rem; line-height: 1.5;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3" style="background: rgba(139, 92, 246, 0.05); border-radius: 12px; border-left: 4px solid #8b5cf6;">
                        <small class="text-muted" style="font-family: 'DM Sans', sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Keterangan</small>
                        <div id="confirm-keterangan" style="font-family: 'DM Sans', sans-serif; color: #374151; margin-top: 0.25rem; line-height: 1.5;"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem 2rem; gap: 1rem;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border: 2px solid #e5e7eb; color: #6b7280; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit" style="background: #64b6f7; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-weight: 600; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-check me-2"></i>Ya, Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus, .form-select:focus {
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

#status_masuk {
    transition: all 0.3s ease;
}

#status_masuk:focus {
    transform: scale(1.02);
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status_masuk');
    const jamMasukInput = document.getElementById('jam_masuk');
    const jamMasukLabel = jamMasukInput.previousElementSibling;
    const fileInput = document.getElementById('evidence_file');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const confirmFileWrapper = document.getElementById('confirm-file-wrapper');
    const confirmFile = document.getElementById('confirm-file');
    
    // Handle status change
    statusSelect.addEventListener('change', function() {
        const status = this.value;
        
        if (status === 'alpa') {
            // Untuk alpa, disable jam masuk
            jamMasukInput.disabled = true;
            jamMasukInput.value = '';
            jamMasukLabel.innerHTML = '<i class="fas fa-clock me-2" style="color: #6b7280;"></i>Jam Masuk <small class="text-muted">(Tidak Hadir)</small>';
            jamMasukInput.style.backgroundColor = '#f3f4f6';
        } else {
            // Untuk izin/sakit, enable jam masuk
            jamMasukInput.disabled = false;
            jamMasukLabel.innerHTML = '<i class="fas fa-clock me-2" style="color: #64b6f7;"></i>Jam Masuk <small class="text-muted">(Opsional)</small>';
            jamMasukInput.style.backgroundColor = '';
        }
    });
    
    // Handle file input change
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Check file size (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 2MB.');
                this.value = '';
                filePreview.style.display = 'none';
                return;
            }
            
            // Show file preview
            fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
            filePreview.style.display = 'block';
        } else {
            filePreview.style.display = 'none';
        }
    });
    
    // Form validation with custom modal
    document.getElementById('manualAbsensiForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default to show modal
        
        const guru = document.getElementById('guru_id').value;
        const tanggal = document.getElementById('tanggal').value;
        const status = document.getElementById('status_masuk').value;
        const jamMasuk = document.getElementById('jam_masuk').value;
        const keterangan = document.getElementById('keterangan_masuk').value.trim();
        const file = fileInput.files[0];
        
        // Basic validation
        if (!guru || !tanggal || !status) {
            showErrorAlert('Mohon lengkapi semua field yang wajib diisi!');
            return;
        }
        
        if (keterangan.length < 10) {
            showErrorAlert('Keterangan harus minimal 10 karakter untuk memberikan informasi yang jelas!');
            return;
        }
        
        // Show confirmation modal
        showConfirmationModal(guru, tanggal, status, jamMasuk, keterangan, file);
    });
    
    // Function to show confirmation modal
    function showConfirmationModal(guruId, tanggal, status, jamMasuk, keterangan, file) {
        const guruSelect = document.getElementById('guru_id');
        const guruName = guruSelect.selectedOptions[0].text;
        const statusSelect = document.getElementById('status_masuk');
        const statusText = statusSelect.selectedOptions[0].text;
        const hasFile = fileInput.files.length > 0;
        
        // Format date
        const dateObj = new Date(tanggal);
        const formattedDate = dateObj.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Update modal content
        document.getElementById('confirm-guru').textContent = guruName;
        document.getElementById('confirm-tanggal').textContent = formattedDate;
        document.getElementById('confirm-status').textContent = statusText;
        document.getElementById('confirm-keterangan').textContent = keterangan;
        
        // Update status icon
        const statusIcon = document.getElementById('confirm-status-icon');
        if (status === 'izin') {
            statusIcon.className = 'fas fa-info-circle';
            statusIcon.style.color = '#64b6f7';
        } else if (status === 'sakit') {
            statusIcon.className = 'fas fa-thermometer-half';
            statusIcon.style.color = '#ef4444';
        } else if (status === 'alpa') {
            statusIcon.className = 'fas fa-times-circle';
            statusIcon.style.color = '#6b7280';
        }
        
        // Handle jam masuk
        const jamWrapper = document.getElementById('confirm-jam-wrapper');
        if (jamMasuk && status !== 'alpa') {
            document.getElementById('confirm-jam').textContent = jamMasuk;
            jamWrapper.style.display = 'block';
        } else {
            jamWrapper.style.display = 'none';
        }
        
        // Handle file info
        if (hasFile) {
            confirmFile.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
            confirmFileWrapper.style.display = 'block';
        } else {
            confirmFileWrapper.style.display = 'none';
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }
    
    // Handle confirmation submit
    document.getElementById('confirmSubmit').addEventListener('click', function() {
        // Submit form actually
        const form = document.getElementById('manualAbsensiForm');
        const formData = new FormData(form);
        
        // Disable button to prevent double submit
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Submit form
        form.submit();
    });
    
    // Function to show error alert
    function showErrorAlert(message) {
        // Create error alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger shadow-sm mb-4';
        alertDiv.style.cssText = 'border: none; border-radius: 8px; background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; animation: slideInDown 0.5s ease-out;';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-2" style="color: #ef4444;"></i>
            <span style="color: #dc2626; font-family: 'DM Sans', sans-serif;">${message}</span>
        `;
        
        // Insert after header
        const header = document.querySelector('.container-fluid .card:first-child');
        header.parentNode.insertBefore(alertDiv, header.nextSibling);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
        
        // Scroll to alert
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Helper functions for file handling
    function removeFile() {
        document.getElementById('evidence_file').value = '';
        document.getElementById('file-preview').style.display = 'none';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection
