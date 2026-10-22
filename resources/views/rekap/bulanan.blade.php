@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Updated header with elegant white design and sky blue accents -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <h2 class="mb-2 fw-bold" style="color: #64b6f7; font-family: 'DM Sans', sans-serif;">Rekap Presensi Bulanan</h2>
                    <!-- Menambahkan navigasi bulan dengan tombol previous/next -->
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        @if($canGoPrev)
                            <a href="{{ route('rekap.bulanan', ['year' => $prevYear, 'month' => $prevMonth]) }}" 
                               class="btn btn-outline-primary me-3" style="border-radius: 8px;">
                                <i class="fas fa-chevron-left"></i> Bulan Sebelum
                            </a>
                        @endif
                        
                        <div class="mx-3">
                            <p class="mb-0 fs-5 fw-bold" style="color: #6b7280; font-family: 'DM Sans', sans-serif;">
                                {{ $start->format('d M') }} - {{ $end->format('d M Y') }}
                            </p>
                            <small style="color: #9ca3af;">{{ $start->format('F Y') }}</small>
                        </div>
                        
                        @if($canGoNext)
                            <a href="{{ route('rekap.bulanan', ['year' => $nextYear, 'month' => $nextMonth]) }}" 
                               class="btn btn-outline-primary ms-3" style="border-radius: 8px;">
                                Bulan Sesudah <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated search bar with elegant white design -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text" style="background: #64b6f7; border: none; color: white; border-radius: 8px 0 0 8px;">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="searchInput" 
                                       placeholder="Cari berdasarkan nama guru, jabatan, atau persentase kehadiran..."
                                       style="border: 2px solid #e5e7eb; border-left: none; border-radius: 0 8px 8px 0; font-family: 'DM Sans', sans-serif;">
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge px-3 py-2 fs-6" id="resultCounter" style="background: #64b6f7; font-family: 'DM Sans', sans-serif;">
                                Total: {{ $data->count() }} Guru/Staff
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated export button to show only PDF with direct download -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #10b981; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                        <i class="fas fa-download me-2 fs-4"></i>
                        <form action="{{ route('rekap.bulanan.export') }}" method="GET" class="d-inline">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <button type="submit" class="btn btn-light fw-bold px-4 py-2" style="border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated table with elegant white design and improved styling -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                <div class="card-header border-0 py-4 d-flex justify-content-between align-items-center" style="background: #f9fafb; border-radius: 12px 12px 0 0;">
                    <div class="row text-center fw-bold flex-grow-1" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                        <div class="col-md-2">
                            <i class="fas fa-user me-2" style="color: #64b6f7;"></i>Nama Guru
                        </div>
                        <div class="col-md-1">
                            <i class="fas fa-briefcase me-2" style="color: #10b981;"></i>Jabatan
                        </div>
                        <div class="col-md-2">
                            <i class="fas fa-calendar-check me-2" style="color: #10b981;"></i>Kehadiran
                        </div>
                        <div class="col-md-2">
                            <i class="fas fa-clock me-2" style="color: #f59e0b;"></i>Terlambat
                        </div>
                        <!-- Mengganti kolom tidak hadir menjadi izin -->
                        <div class="col-md-2">
                            <i class="fas fa-info-circle me-2" style="color: #3b82f6;"></i>Izin
                        </div>
                        <div class="col-md-1">
                            <i class="fas fa-percentage me-2" style="color: #64b6f7;"></i>Persentase
                        </div>
                        <div class="col-md-2">
                            <i class="fas fa-bars me-2" style="color: #64b6f7;"></i>Aksi
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" id="dataContainer">
                    @foreach($data as $index => $guru)
                        @php
                            $workDays = 0;
                            $current = $start->copy();
                            $dayMapping = [
                                1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 
                                5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'
                            ];
                            
                            while ($current <= $end) {
                                $dayOfWeek = $current->dayOfWeek == 0 ? 7 : $current->dayOfWeek;
                                $hariIndonesia = $dayMapping[$dayOfWeek];
                                
                                $schedule = $guru->weeklySchedules->where('hari', $hariIndonesia)->first();
                                if ($schedule && $schedule->is_active) {
                                    $workDays++;
                                }
                                $current->addDay();
                            }
                            
                            $hadir = $guru->absensi->where('status_masuk', 'hadir')->count();
                            $terlambat = $guru->absensi->where('status_masuk', 'terlambat')->count();
                            $izin = $guru->absensi->where('status_masuk', 'izin')->count();
                            $sakit = $guru->absensi->where('status_masuk', 'sakit')->count();
                            $alpa = $guru->absensi->where('status_masuk', 'alpa')->count();
                            
                            $totalKehadiran = $hadir + $terlambat; // Hanya hadir dan terlambat yang dianggap kehadiran fisik
                            $percentage = $workDays > 0 ? ($totalKehadiran / $workDays) * 100 : 0;
                        @endphp
                        <div class="row py-3 mx-0 position-relative overflow-hidden guru-row" 
                             style="transition: all 0.3s ease; cursor: pointer; border-bottom: 1px solid #f3f4f6;"
                             data-nama="{{ strtolower($guru->nama) }}" 
                             data-jabatan="{{ strtolower($guru->jabatan) }}"
                             data-kehadiran="{{ $totalKehadiran }}"
                             data-terlambat="{{ $terlambat }}"
                             data-izin="{{ $izin }}"
                             data-persentase="{{ round($percentage, 1) }}"
                             onmouseover="this.style.background='rgba(100, 182, 247, 0.05)'; this.style.transform='translateX(5px)'"
                             onmouseout="this.style.background=''; this.style.transform='translateX(0)'">
                            
                            <!-- Updated number badge with sky blue styling -->
                            <div class="position-absolute start-0 top-50 translate-middle-y ms-2">
                                <span class="badge rounded-pill" style="background: #64b6f7; font-size: 0.7rem; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-family: 'DM Sans', sans-serif;">
                                    {{ $index + 1 }}
                                </span>
                            </div>

                            <div class="col-md-2 ps-5">
                                <div class="d-flex align-items-center">
                                    <!-- Updated avatar with sky blue background -->
                                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background: #64b6f7; color: white; font-weight: bold; font-family: 'DM Sans', sans-serif;">
                                        {{ strtoupper(substr($guru->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">{{ $guru->nama }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-1 d-flex align-items-center justify-content-center">
                                <!-- Updated badge with elegant styling -->
                                <span class="badge px-2 py-1 rounded-pill" 
                                      style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.75rem; font-family: 'DM Sans', sans-serif;">
                                    {{ $guru->jabatan }}
                                </span>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <!-- Updated circle with green color -->
                                    <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; background: #10b981; color: white; font-weight: bold; font-size: 1.1rem; font-family: 'DM Sans', sans-serif;">
                                        {{ $totalKehadiran }}
                                    </div>
                                    <small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">hari</small>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <!-- Updated circle with orange color -->
                                    <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; background: #f59e0b; color: white; font-weight: bold; font-size: 1.1rem; font-family: 'DM Sans', sans-serif;">
                                        {{ $terlambat }}
                                    </div>
                                    <small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">kali</small>
                                </div>
                            </div>

                            <!-- Mengganti kolom tidak hadir menjadi izin -->
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; background: #3b82f6; color: white; font-weight: bold; font-size: 1.1rem; font-family: 'DM Sans', sans-serif;">
                                        {{ $izin }}
                                    </div>
                                    <small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">hari</small>
                                </div>
                            </div>

                            <div class="col-md-1 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <!-- Updated circle with sky blue color -->
                                    <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; background: #64b6f7; color: white; font-weight: bold; font-size: 0.9rem; font-family: 'DM Sans', sans-serif;">
                                        {{ round($percentage, 1) }}%
                                    </div>
                                    <small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">hadir</small>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <!-- Updated button with sky blue styling -->
                                <button class="btn btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailModal"
                                        onclick="populateDetailModal({{ $index }}, '{{ $guru->nama }}', {{ json_encode($guru->absensi->sortBy('tanggal')->values()) }})"
                                        style="border-radius: 8px; background: rgba(100, 182, 247, 0.1); color: #64b6f7; border: 1px solid rgba(100, 182, 247, 0.2); font-family: 'DM Sans', sans-serif;">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Updated no results message with elegant white design -->
                <div id="noResults" class="text-center py-5" style="display: none;">
                    <div class="card-body py-5">
                        <i class="fas fa-search mb-3" style="font-size: 3rem; color: #9ca3af;"></i>
                        <h5 style="color: #6b7280; font-family: 'DM Sans', sans-serif;">Tidak ada hasil yang ditemukan</h5>
                        <p class="mb-0" style="color: #9ca3af; font-family: 'DM Sans', sans-serif;">Coba gunakan kata kunci yang berbeda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated summary card with elegant white design -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #10b981;">
                <div class="card-body text-center py-3">
                    <div class="row">
                        <div class="col-md-3">
                            <h4 class="text-white mb-1" style="font-family: 'DM Sans', sans-serif;">{{ count($data) }}</h4>
                            <small class="text-white" style="opacity: 0.8; font-family: 'DM Sans', sans-serif;">Total Guru</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-white mb-1" style="font-family: 'DM Sans', sans-serif;">
                                {{ $summary['hadir'] ?? 0 }}
                            </h4>
                            <small class="text-white" style="opacity: 0.8; font-family: 'DM Sans', sans-serif;">Total Hadir</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-white mb-1" style="font-family: 'DM Sans', sans-serif;">{{ $summary['terlambat'] ?? 0 }}</h4>
                            <small class="text-white" style="opacity: 0.8; font-family: 'DM Sans', sans-serif;">Total Terlambat</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-white mb-1" style="font-family: 'DM Sans', sans-serif;">{{ $summary['izin'] ?? 0 }}</h4>
                            <small class="text-white" style="opacity: 0.8; font-family: 'DM Sans', sans-serif;">Total Izin</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius: 12px;">
            <div class="modal-header border-0 py-4" style="background: linear-gradient(135deg, #64b6f7 0%, #3b82f6 100%);">
                <div>
                    <h5 class="modal-title text-white fw-bold" id="detailModalLabel" style="font-family: 'DM Sans', sans-serif;">
                        Detail Absensi
                    </h5>
                    <small class="text-white" style="opacity: 0.8;" id="modalGuruName"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="detailContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .detail-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        box-shadow: 0 4px 12px rgba(100, 182, 247, 0.1);
        transform: translateY(-2px);
    }

    .status-badge {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
    }

    .status-hadir {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .status-terlambat {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .status-izin {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .status-sakit {
        background: rgba(107, 114, 128, 0.1);
        color: #6b7280;
    }

    .status-alpa {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    /* Updated styles with elegant white theme and sky blue accents */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-1px);
    }

    .form-control:focus {
        border-color: #64b6f7;
        box-shadow: 0 0 0 0.2rem rgba(100, 182, 247, 0.25);
    }
</style>

<!-- Menambahkan JavaScript untuk fitur pencarian dan modal -->
<script>
function populateDetailModal(index, guruName, absensiData) {
    const modalGuruName = document.getElementById('modalGuruName');
    const detailContent = document.getElementById('detailContent');
    
    modalGuruName.textContent = guruName;
    
    if (!absensiData || absensiData.length === 0) {
        detailContent.innerHTML = `
            <div class="alert alert-info" role="alert" style="border-radius: 8px; border: none; background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-family: 'DM Sans', sans-serif;">
                <i class="fas fa-info-circle me-2"></i>Tidak ada data absensi pada periode ini.
            </div>
        `;
        return;
    }

    let html = '<div class="row">';
    absensiData.forEach((absen, idx) => {
        const statusClass = {
            'hadir': 'status-hadir',
            'terlambat': 'status-terlambat',
            'izin': 'status-izin',
            'sakit': 'status-sakit',
            'alpa': 'status-alpa'
        }[absen.status_masuk] || '';

        const tanggal = new Date(absen.tanggal).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        html += `
            <div class="col-md-6 mb-3">
                <div class="detail-card p-3" style="background: #f9fafb;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1" style="color: #374151; font-family: 'DM Sans', sans-serif;">
                                ${tanggal}
                            </h6>
                            <small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-clock me-1"></i>
                                Masuk: <strong>${absen.jam_masuk}</strong>
                                ${absen.jam_keluar ? `<br><i class="fas fa-door-open me-1"></i>Keluar: <strong>${absen.jam_keluar}</strong>` : ''}
                            </small>
                        </div>
                        <span class="status-badge ${statusClass}">
                            ${absen.status_masuk.charAt(0).toUpperCase() + absen.status_masuk.slice(1)}
                        </span>
                    </div>
                    ${absen.keterangan ? `
                        <div class="mt-2 pt-2 border-top" style="border-top-color: #e5e7eb;">
                            <small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-sticky-note me-1"></i>
                                ${absen.keterangan}
                            </small>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    });

    html += '</div>';
    detailContent.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const resultCounter = document.getElementById('resultCounter');
    const dataContainer = document.getElementById('dataContainer');
    const noResults = document.getElementById('noResults');
    const guruRows = document.querySelectorAll('.guru-row');
    const totalData = {{ $data->count() }};

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        guruRows.forEach(row => {
            const nama = row.dataset.nama;
            const jabatan = row.dataset.jabatan;
            const kehadiran = row.dataset.kehadiran;
            const terlambat = row.dataset.terlambat;
            const izin = row.dataset.izin;
            const persentase = row.dataset.persentase;

            const isVisible = searchTerm === '' || 
                            nama.includes(searchTerm) || 
                            jabatan.includes(searchTerm) ||
                            kehadiran.includes(searchTerm) ||
                            terlambat.includes(searchTerm) ||
                            izin.includes(searchTerm) ||
                            persentase.includes(searchTerm);

            if (isVisible) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update counter
        resultCounter.textContent = `Total: ${visibleCount} Guru/Staff`;

        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    searchInput.addEventListener('input', performSearch);
    searchInput.addEventListener('keyup', performSearch);
});
</script>
@endsection
