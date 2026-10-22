@extends('layouts.app')

@section('title', 'Kelola Bukti Absensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-file-image me-2"></i>
                            Kelola Bukti Presensi
                        </h5>
                        <small>Kelola dan lihat semua bukti pendukung presensi guru & staff</small>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar text-success"></i>
                                    Tanggal Dari:
                                </label>
                                <input type="date" name="tanggal_dari" class="form-control" 
                                       value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar text-success"></i>
                                    Tanggal Sampai:
                                </label>
                                <input type="date" name="tanggal_sampai" class="form-control" 
                                       value="{{ request('tanggal_sampai') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-user text-info"></i>
                                    Guru/Staff:
                                </label>
                                <select name="guru_id" class="form-select">
                                    <option value="">-- Semua Guru/Staff --</option>
                                    @foreach($guru as $g)
                                        <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-info-circle text-warning"></i>
                                    Status:
                                </label>
                                <select name="status" class="form-select">
                                    <option value="">-- Semua Status --</option>
                                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="alpa" {{ request('status') == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('absensi.evidence.list') }}" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Evidence List -->
                    @if($evidences->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-calendar"></i> Tanggal</th>
                                        <th><i class="fas fa-user"></i> Nama</th>
                                        <th><i class="fas fa-id-badge"></i> NIP</th>
                                        <th><i class="fas fa-info-circle"></i> Status</th>
                                        <th><i class="fas fa-clock"></i> Jam</th>
                                        <th><i class="fas fa-comment"></i> Keterangan</th>
                                        <th><i class="fas fa-file"></i> Bukti</th>
                                        <th><i class="fas fa-cogs"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evidences as $evidence)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-day text-primary me-2"></i>
                                                    <div>
                                                        <strong>{{ $evidence->tanggal->format('d/m/Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $evidence->tanggal->format('l') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($evidence->guru->nama, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $evidence->guru->nama }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $evidence->guru->jabatan }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $evidence->guru->nip }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($evidence->status_masuk) {
                                                        'hadir' => 'success',
                                                        'terlambat' => 'warning',
                                                        'izin' => 'info',
                                                        'sakit' => 'secondary',
                                                        'alpa' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ ucfirst($evidence->status_masuk) }}
                                                </span>
                                                @if($evidence->is_manual)
                                                    <br><small class="badge bg-warning mt-1">Manual</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($evidence->jam_masuk)
                                                    <i class="fas fa-sign-in-alt text-success"></i>
                                                    {{ Carbon\Carbon::parse($evidence->jam_masuk)->format('H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="max-width: 200px;">
                                                    @if($evidence->keterangan_masuk)
                                                        <small>{{ Str::limit($evidence->keterangan_masuk, 50) }}</small>
                                                    @else
                                                        <span class="text-muted">Tidak ada keterangan</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($evidence->hasEvidence())
                                                    @php
                                                        $extension = pathinfo($evidence->evidence_file, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                                                    @endphp
                                                    
                                                    @if($isImage)
                                                        <i class="fas fa-image text-success"></i>
                                                        <small class="text-success">Gambar</small>
                                                    @else
                                                        <i class="fas fa-file-alt text-info"></i>
                                                        <small class="text-info">Dokumen</small>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ strtoupper($extension) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($evidence->hasEvidence())
                                                        <button type="button" class="btn btn-sm btn-info" 
                                                                onclick="viewEvidence({{ $evidence->id }})"
                                                                title="Lihat Bukti">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="{{ route('absensi.evidence.download', $evidence->id) }}" 
                                                           class="btn btn-sm btn-success" title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('absensi.evidence.delete', $evidence->id) }}" 
                                                              class="d-inline" onsubmit="return confirm('Yakin ingin menghapus bukti ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Bukti">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('absensi.edit.keterangan', $evidence->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit Keterangan">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Menampilkan {{ $evidences->firstItem() }} - {{ $evidences->lastItem() }} 
                                    dari {{ $evidences->total() }} bukti
                                </small>
                            </div>
                            <div>
                                {{ $evidences->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-image fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada bukti presensi</h5>
                            <p class="text-muted">Belum ada bukti pendukung presensi yang diupload</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk melihat bukti -->
<div class="modal fade" id="evidenceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-image text-primary"></i>
                    Bukti Pendukung Presensi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="evidenceContent">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <a href="#" id="downloadBtn" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function viewEvidence(id) {
    const modal = new bootstrap.Modal(document.getElementById('evidenceModal'));
    const content = document.getElementById('evidenceContent');
    const downloadBtn = document.getElementById('downloadBtn');
    
    // Reset content
    content.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    
    // Set download link
    downloadBtn.href = `/absensi/${id}/download-evidence`;
    
    // Show modal
    modal.show();
    
    // Fetch evidence data
    fetch(`/absensi/${id}/evidence`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const evidenceUrl = data.data.evidence_url;
                const extension = evidenceUrl.split('.').pop().toLowerCase();
                
                content.innerHTML = `
                    <div class="mb-3">
                        <h6>${data.data.guru_nama}</h6>
                        <p class="text-muted">Tanggal: ${data.data.tanggal} - Status: ${data.data.status}</p>
                    </div>
                `;
                
                if (['jpg', 'jpeg', 'png'].includes(extension)) {
                    content.innerHTML += `
                        <img src="${evidenceUrl}" class="img-fluid rounded shadow" 
                             style="max-height: 400px;" alt="Bukti Absensi">
                    `;
                } else {
                    content.innerHTML += `
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-4x text-info mb-3"></i>
                            <h5>Dokumen Bukti Pendukung</h5>
                            <p class="text-muted">Klik tombol download untuk melihat dokumen</p>
                        </div>
                    `;
                }
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Terjadi kesalahan saat memuat bukti
                </div>
            `;
        });
}
</script>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
    font-weight: bold;
}
</style>
@endsection
