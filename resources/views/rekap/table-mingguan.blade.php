@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header dengan navigasi minggu -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <h2 class="mb-2 fw-bold" style="color: #64b6f7; font-family: 'DM Sans', sans-serif;">Rekap Presensi Mingguan (Tabel)</h2>
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        @if($canGoPrev)
                            <a href="{{ route('rekap.mingguan.table', ['year' => $prevYear, 'week' => $prevWeek]) }}" 
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
                            <a href="{{ route('rekap.mingguan.table', ['year' => $nextYear, 'week' => $nextWeek]) }}" 
                               class="btn btn-outline-primary ms-3" style="border-radius: 8px;">
                                Minggu Sesudah <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #007acc; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                        <i class="fas fa-download me-2 fs-4"></i>
                        <form action="{{ route('rekap.mingguan.export.table') }}" method="GET" class="d-inline">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="week" value="{{ $week }}">
                            <input type="hidden" name="format" value="excel">
                            <button type="submit" class="btn btn-light fw-bold px-3 py-2" style="border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </button>
                        </form>
                        <form action="{{ route('rekap.mingguan.export.table') }}" method="GET" class="d-inline">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="week" value="{{ $week }}">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="btn btn-light fw-bold px-3 py-2" style="border-radius: 8px; font-family: 'DM Sans', sans-serif;">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Table View -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                <div class="card-body p-3" style="overflow-x: auto;">
                    <table class="table table-bordered table-hover mb-0" style="font-size: 0.9rem; font-family: 'DM Sans', sans-serif;">
                        <thead style="background: #1a4d7a; color: white;">
                            <tr>
                                <th style="width: 150px; text-align: center; vertical-align: middle;">No / Nama Guru</th>
                                <th style="text-align: center; vertical-align: middle;">Jabatan</th>
                                @foreach($dates as $date)
                                    <th style="width: 45px; text-align: center; vertical-align: middle; background: #2c6ba8;">
                                        <div style="font-size: 0.75rem;">{{ $date->format('d') }}</div>
                                        <div style="font-size: 0.7rem;">{{ substr($date->format('l'), 0, 3) }}</div>
                                    </th>
                                @endforeach
                                <th style="width: 60px; text-align: center; vertical-align: middle;">Hadir</th>
                                <th style="width: 60px; text-align: center; vertical-align: middle;">Terlambat</th>
                                <th style="width: 60px; text-align: center; vertical-align: middle;">Izin</th>
                                <th style="width: 60px; text-align: center; vertical-align: middle;">Sakit</th>
                                <th style="width: 60px; text-align: center; vertical-align: middle;">Alpa</th>
                                <th style="width: 70px; text-align: center; vertical-align: middle;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalByStatus = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
                            @endphp
                            @foreach($data as $index => $guru)
                                @php
                                    $workDays = 0;
                                    $current = $start->copy();
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
                                    
                                    $totalByStatus['hadir'] += $hadir;
                                    $totalByStatus['terlambat'] += $terlambat;
                                    $totalByStatus['izin'] += $izin;
                                    $totalByStatus['sakit'] += $sakit;
                                    $totalByStatus['alpa'] += $alpa;
                                    
                                    $totalKehadiran = $hadir + $terlambat;
                                    $percentage = $workDays > 0 ? ($totalKehadiran / $workDays) * 100 : 0;
                                    
                                    $dayMapping = [
                                        1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 
                                        5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'
                                    ];
                                @endphp
                                <tr style="background: @if($index % 2 == 0) #f9fafb @else #ffffff @endif">
                                    <td style="font-weight: 600; color: #374151;">
                                        <span class="badge rounded-circle" style="background: #64b6f7; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">{{ $index + 1 }}</span>
                                        <div style="margin-top: 5px;">{{ $guru->nama }}</div>
                                    </td>
                                    <td style="text-align: center; font-size: 0.85rem;"><span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">{{ $guru->jabatan }}</span></td>
                                    
                                    {{-- Daily attendance cells --}}
                                    @foreach($dates as $date)
                                        @php
                                            $absensi = $guru->absensi->where('tanggal', $date->format('Y-m-d'))->first();
                                            $status = $absensi ? $absensi->status_masuk : null;
                                            
                                            $statusColor = match($status) {
                                                'hadir' => '#10b981',
                                                'terlambat' => '#f59e0b',
                                                'izin' => '#3b82f6',
                                                'sakit' => '#8b5cf6',
                                                'alpa' => '#ef4444',
                                                default => '#e5e7eb'
                                            };
                                            
                                            $statusText = match($status) {
                                                'hadir' => 'H',
                                                'terlambat' => 'T',
                                                'izin' => 'I',
                                                'sakit' => 'S',
                                                'alpa' => 'A',
                                                default => '-'
                                            };
                                        @endphp
                                        <td style="text-align: center; padding: 8px 2px;">
                                            <div class="badge" style="background: {{ $statusColor }}; color: white; width: 35px; display: inline-flex; align-items: center; justify-content: center; font-weight: bold;">
                                                {{ $statusText }}
                                            </div>
                                        </td>
                                    @endforeach
                                    
                                    <td style="text-align: center; font-weight: 600; color: #10b981;">{{ $hadir }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #f59e0b;">{{ $terlambat }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #3b82f6;">{{ $izin }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #8b5cf6;">{{ $sakit }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #ef4444;">{{ $alpa }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #64b6f7;">{{ round($percentage, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #f0f0f0; font-weight: bold;">
                            <tr>
                                <td colspan="2" style="text-align: right;">TOTAL:</td>
                                <td colspan="{{ count($dates) }}" style="text-align: center;"></td>
                                <td style="text-align: center; color: #10b981;">{{ $totalByStatus['hadir'] }}</td>
                                <td style="text-align: center; color: #f59e0b;">{{ $totalByStatus['terlambat'] }}</td>
                                <td style="text-align: center; color: #3b82f6;">{{ $totalByStatus['izin'] }}</td>
                                <td style="text-align: center; color: #8b5cf6;">{{ $totalByStatus['sakit'] }}</td>
                                <td style="text-align: center; color: #ef4444;">{{ $totalByStatus['alpa'] }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Keterangan -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #f9fafb;">
                <div class="card-body p-3" style="font-size: 0.9rem; font-family: 'DM Sans', sans-serif;">
                    <h6 class="fw-bold mb-3">Keterangan Status:</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <span class="badge" style="background: #10b981; padding: 8px 12px;">H</span> = Hadir
                        </div>
                        <div class="col-md-3">
                            <span class="badge" style="background: #f59e0b; padding: 8px 12px;">T</span> = Terlambat
                        </div>
                        <div class="col-md-3">
                            <span class="badge" style="background: #3b82f6; padding: 8px 12px;">I</span> = Izin
                        </div>
                        <div class="col-md-3">
                            <span class="badge" style="background: #8b5cf6; padding: 8px 12px;">S</span> = Sakit
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <span class="badge" style="background: #ef4444; padding: 8px 12px;">A</span> = Alpa
                        </div>
                        <div class="col-md-3">
                            <span class="badge" style="background: #e5e7eb; color: #333; padding: 8px 12px;">-</span> = Libur/Tidak Kerja
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
