@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3" style="min-height: 100vh; overflow-y: auto;">
    <!-- Updated header with elegant white design and sky blue accents -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <h2 class="mb-2 fw-bold" style="color: #64b6f7; font-family: 'DM Sans', sans-serif;">Rekap Presensi Mingguan</h2>
                    <!-- Menambahkan navigasi minggu dengan tombol previous/next -->
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        @if($canGoPrev)
                            <a href="{{ route('rekap.mingguan', ['year' => $prevYear, 'week' => $prevWeek]) }}" 
                               class="btn btn-outline-primary me-3" style="border-radius: 8px;">
                                <i class="fas fa-chevron-left"></i> Minggu Sebelum
                            </a>
                        @endif
                        
                        <div class="mx-3">
                            <p class="mb-0 fs-5 fw-bold" style="color: #6b7280; font-family: 'DM Sans', sans-serif;">
                                {{ $start->format('d M') }} - {{ $end->format('d M Y') }}
                            </p>
                            <small style="color: #9ca3af;">Minggu ke-{{ $week }}, {{ $year }}</small>
                        </div>
                        
                        @if($canGoNext)
                            <a href="{{ route('rekap.mingguan', ['year' => $nextYear, 'week' => $nextWeek]) }}" 
                               class="btn btn-outline-primary ms-3" style="border-radius: 8px;">
                                Minggu Sesudah <i class="fas fa-chevron-right"></i>
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
                                       placeholder="Cari berdasarkan nama guru, jabatan, atau status kehadiran..."
                                       style="border: 2px solid #e5e7eb; border-left: none; border-radius: 0 8px 8px 0; font-family: 'DM Sans', sans-serif;">
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge px-3 py-2 fs-6" id="resultCounter" style="background: #64b6f7; font-family: 'DM Sans', sans-serif;">
                                Total: {{ count($data) }} Guru/Staff
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated summary statistics cards with new color scheme -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: #64b6f7; border-radius: 12px;">
                <div class="card-body text-white text-center">
                    <div class="fs-1 mb-2"><i class="fas fa-users"></i></div>
                    <h4 class="fw-bold">{{ count($data) }}</h4>
                    <p class="mb-0">Total Staff</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: #10b981; border-radius: 12px;">
                <div class="card-body text-white text-center">
                    <div class="fs-1 mb-2"><i class="fas fa-check-circle"></i></div>
                    {{-- Gunakan $summary dari controller bukan perhitungan manual di view --}}
                    <h4 class="fw-bold">{{ $summary['hadir'] ?? 0 }}</h4>
                    <p class="mb-0">Total Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: #f59e0b; border-radius: 12px;">
                <div class="card-body text-white text-center">
                    <div class="fs-1 mb-2"><i class="fas fa-clock"></i></div>
                    {{-- Gunakan $summary dari controller --}}
                    <h4 class="fw-bold">{{ $summary['terlambat'] ?? 0 }}</h4>
                    <p class="mb-0">Total Terlambat</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: #3b82f6; border-radius: 12px;">
                <div class="card-body text-white text-center">
                    <div class="fs-1 mb-2"><i class="fas fa-info-circle"></i></div>
                    {{-- Gunakan $summary dari controller --}}
                    <h4 class="fw-bold">{{ $summary['izin'] ?? 0 }}</h4>
                    <p class="mb-0">Total Izin</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated export button to show only PDF with direct download -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #007acc; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                        <i class="fas fa-download me-2 fs-4"></i>
                        <form action="{{ route('rekap.mingguan.export') }}" method="GET" class="d-inline">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="week" value="{{ $week }}">
                            <button type="submit" class="btn btn-light fw-bold px-4 py-2" style="border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="searchResults">
        <!-- Updated card-based data display with elegant white design -->
        <div class="row" id="dataContainer">
            @foreach($data as $index => $guru)
                <div class="col-lg-6 col-md-12 mb-4 guru-card" 
                     data-nama="{{ strtolower($guru->nama) }}" 
                     data-jabatan="{{ strtolower($guru->jabatan) }}"
                     {{-- Mengupdate data attributes untuk pencarian yang benar --}}
                     data-kehadiran="{{ $guru->absensi->whereIn('status_masuk', ['hadir', 'terlambat'])->count() }}"
                     data-terlambat="{{ $guru->absensi->where('status_masuk', 'terlambat')->count() }}"
                     data-izin="{{ $guru->absensi->where('status_masuk', 'izin')->count() }}">
                    <div class="card border-0 shadow-sm h-100 attendance-card" style="border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.3s ease; background: #ffffff;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <!-- Updated avatar with sky blue background -->
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 60px; height: 60px; background: #64b6f7; color: white; font-weight: bold; font-size: 1.2rem; font-family: 'DM Sans', sans-serif;">
                                    {{ strtoupper(substr($guru->nama, 0, 2)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1 fw-bold" style="color: #374151; font-family: 'DM Sans', sans-serif;">{{ $guru->nama }}</h5>
                                    <!-- Updated badge with sky blue styling -->
                                    <span class="badge rounded-pill px-3 py-2" 
                                          style="background: rgba(100, 182, 247, 0.1); color: #64b6f7; border: 1px solid rgba(100, 182, 247, 0.2); font-size: 0.8rem; font-family: 'DM Sans', sans-serif;">
                                        {{ $guru->jabatan }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Updated statistics with new color scheme -->
                            <div class="row text-center mb-3">
                                <div class="col-3">
                                    <div class="attendance-stat">
                                        {{-- Menghitung hanya yang benar-benar hadir --}}
                                        <div class="stat-number" style="color: #10b981;">{{ $guru->absensi->whereIn('status_masuk', ['hadir', 'terlambat'])->count() }}</div>
                                        <div class="stat-label">Hadir</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="attendance-stat">
                                        <div class="stat-number" style="color: #f59e0b;">{{ $guru->absensi->where('status_masuk', 'terlambat')->count() }}</div>
                                        <div class="stat-label">Terlambat</div>
                                    </div>
                                </div>
                                <!-- Mengganti kolom Tidak Hadir menjadi Izin -->
                                <div class="col-3">
                                    <div class="attendance-stat">
                                        <div class="stat-number" style="color: #3b82f6;">{{ $guru->absensi->where('status_masuk', 'izin')->count() }}</div>
                                        <div class="stat-label">Izin</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="attendance-stat">
                                        @php
                                            $kehadiran = $guru->absensi->whereIn('status_masuk', ['hadir', 'terlambat'])->count();
                                            
                                            // Hitung hari kerja berdasarkan jadwal mingguan guru
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
                                            
                                            $percentage = $workDays > 0 ? ($kehadiran / $workDays) * 100 : 0;
                                        @endphp
                                        <div class="stat-number" style="color: #64b6f7;">{{ round($percentage, 1) }}%</div>
                                        <div class="stat-label">Kehadiran</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Updated attendance details with elegant styling -->
                            <div class="attendance-details">
                                <h6 class="mb-2" style="color: #6b7280; font-family: 'DM Sans', sans-serif;">Detail Absensi:</h6>
                                <div class="attendance-list" style="max-height: 150px; overflow-y: auto; scrollbar-width: thin;">
                                    @foreach($guru->absensi->sortBy('tanggal') as $absen)
                                        <div class="attendance-item d-flex justify-content-between align-items-center py-2 px-3 mb-2 rounded" 
                                             style="background: #f9fafb; border-left: 3px solid 
                                             @if($absen->status_masuk == 'hadir') #10b981
                                             @elseif($absen->status_masuk == 'terlambat') #f59e0b
                                             @elseif($absen->status_masuk == 'izin') #3b82f6
                                             @elseif($absen->status_masuk == 'sakit') #8b5cf6
                                             @else #ef4444 @endif; border-radius: 8px;">
                                            <div>
                                                <div class="fw-semibold" style="color: #374151; font-family: 'DM Sans', sans-serif;">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d M') }}</div>
                                                <small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">
                                                    {{-- Menggunakan jam_masuk dari database tanpa parsing ulang --}}
                                                    @if($absen->jam_masuk)
                                                        Masuk: {{ $absen->jam_masuk }}
                                                    @endif
                                                    @if($absen->jam_keluar)
                                                        | Keluar: {{ $absen->jam_keluar }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge rounded-pill" style="font-family: 'DM Sans', sans-serif;
                                                    @if($absen->status_masuk == 'hadir') background: #10b981;
                                                    @elseif($absen->status_masuk == 'terlambat') background: #f59e0b;
                                                    @elseif($absen->status_masuk == 'izin') background: #3b82f6;
                                                    @elseif($absen->status_masuk == 'sakit') background: #8b5cf6;
                                                    @else background: #ef4444; @endif color: white;">
                                                    {{ ucfirst($absen->status_masuk) }}
                                                </span>
                                                @if($absen->status_keluar)
                                                    <br><small style="color: #6b7280; font-family: 'DM Sans', sans-serif;">{{ $absen->status_keluar }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Updated no results message with elegant white design -->
        <div id="noResults" class="text-center py-5" style="display: none;">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                <div class="card-body py-5">
                    <i class="fas fa-search mb-3" style="font-size: 3rem; color: #9ca3af;"></i>
                    <h5 style="color: #6b7280; font-family: 'DM Sans', sans-serif;">Tidak ada hasil yang ditemukan</h5>
                    <p class="mb-0" style="color: #9ca3af; font-family: 'DM Sans', sans-serif;">Coba gunakan kata kunci yang berbeda</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Updated styles with elegant white theme and sky blue accents */
.attendance-card {
    transition: all 0.3s ease;
}

.attendance-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(100, 182, 247, 0.15) !important;
    border-color: #64b6f7 !important;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
    font-family: 'DM Sans', sans-serif;
}

.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'DM Sans', sans-serif;
}

.attendance-details {
    border-top: 1px solid #e5e7eb;
    padding-top: 1rem;
}

.attendance-item {
    transition: all 0.2s ease;
}

.attendance-item:hover {
    background: #f3f4f6 !important;
    transform: translateX(5px);
}

.form-control:focus {
    border-color: #64b6f7;
    box-shadow: 0 0 0 0.2rem rgba(100, 182, 247, 0.25);
}

/* Updated styles with elegant white theme and sky blue accents */
body {
    overflow-x: hidden;
    overflow-y: auto;
}

.attendance-list::-webkit-scrollbar {
    width: 4px;
}

.attendance-list::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.attendance-list::-webkit-scrollbar-thumb {
    background: #64b6f7;
    border-radius: 2px;
}

.attendance-list::-webkit-scrollbar-thumb:hover {
    background: #3b82f6;
}
</style>

<!-- Menambahkan JavaScript untuk fitur pencarian -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const resultCounter = document.getElementById('resultCounter');
    const dataContainer = document.getElementById('dataContainer');
    const noResults = document.getElementById('noResults');
    const guruCards = document.querySelectorAll('.guru-card');
    const totalData = {{ count($data) }};

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        guruCards.forEach(card => {
            const nama = card.dataset.nama;
            const jabatan = card.dataset.jabatan;
            const kehadiran = card.dataset.kehadiran;
            const terlambat = card.dataset.terlambat;
            const izin = card.dataset.izin;

            const isVisible = searchTerm === '' || 
                            nama.includes(searchTerm) || 
                            jabatan.includes(searchTerm) ||
                            kehadiran.includes(searchTerm) ||
                            terlambat.includes(searchTerm) ||
                            izin.includes(searchTerm);

            if (isVisible) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update counter
        resultCounter.textContent = `Total: ${visibleCount} Guru/Staff`;

        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            dataContainer.style.display = 'none';
            noResults.style.display = 'block';
        } else {
            dataContainer.style.display = 'flex';
            noResults.style.display = 'none';
        }
    }

    searchInput.addEventListener('input', performSearch);
    searchInput.addEventListener('keyup', performSearch);
});
</script>
@endsection
