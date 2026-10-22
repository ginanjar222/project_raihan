@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3" style="min-height: 100vh;">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <h2 class="mb-2 fw-bold" style="color: #64b6f7; font-family: 'DM Sans', sans-serif;">Rekap Presensi Custom Filter</h2>
                    <p class="mb-0" style="color: #6b7280;">Pilih rentang tanggal untuk melihat rekap presensi guru dengan detail input-output</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('rekap.index') }}" id="filterForm" class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label fw-bold" style="color: #374151;">Dari Tanggal:</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ $start_date }}"
                                   required
                                   style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 8px 12px;">
                        </div>

                        <div class="col-md-3">
                            <label for="end_date" class="form-label fw-bold" style="color: #374151;">Sampai Tanggal:</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ $end_date }}"
                                   required
                                   style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 8px 12px;">
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn w-100" style="background: #64b6f7; color: white; border-radius: 8px; font-weight: bold;">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                        </div>

                        <div class="col-md-4 text-end">
                            <button type="button" class="btn me-2" style="background: #e5e7eb; color: #374151; border-radius: 8px;" onclick="setThisMonth()">
                                <i class="fas fa-calendar-alt me-1"></i>Bulan Ini
                            </button>
                            <button type="button" class="btn" style="background: #e5e7eb; color: #374151; border-radius: 8px;" onclick="setThisWeek()">
                                <i class="fas fa-calendar-week me-1"></i>Minggu Ini
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #64b6f7 0%, #3b82f6 100%); border-radius: 12px; color: white;">
                <div class="card-body p-3">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="fs-5 fw-bold">{{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}</div>
                            <small style="opacity: 0.9;">Tanggal Mulai</small>
                        </div>
                        <div class="col-md-3">
                            <i class="fas fa-arrow-right fs-3"></i>
                        </div>
                        <div class="col-md-3">
                            <div class="fs-5 fw-bold">{{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</div>
                            <small style="opacity: 0.9;">Tanggal Akhir</small>
                        </div>
                        <div class="col-md-3">
                            <div class="fs-5 fw-bold">{{ $summary['total_guru'] }}</div>
                            <small style="opacity: 0.9;">Total Guru/Staff</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #f9fafb; border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span style="color: #6b7280; font-weight: bold;">Unduh Laporan:</span>
                        
                        <form action="{{ route('rekap.export') }}" method="GET" class="d-inline">
                            <input type="hidden" name="start_date" value="{{ $start_date }}">
                            <input type="hidden" name="end_date" value="{{ $end_date }}">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="btn" style="background: #ef4444; color: white; border-radius: 8px; font-weight: bold;">
                                <i class="fas fa-file-pdf me-2"></i>PDF Profesional
                            </button>
                        </form>

                        <form action="{{ route('rekap.export') }}" method="GET" class="d-inline">
                            <input type="hidden" name="start_date" value="{{ $start_date }}">
                            <input type="hidden" name="end_date" value="{{ $end_date }}">
                            <input type="hidden" name="format" value="excel">
                            <button type="submit" class="btn" style="background: #10b981; color: white; border-radius: 8px; font-weight: bold;">
                                <i class="fas fa-file-excel me-2"></i>Excel Profesional
                            </button>
                        </form>

                        <button type="button" class="btn ms-auto" style="background: #64b6f7; color: white; border-radius: 8px;" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm" style="background: #64b6f7; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="fs-3 fw-bold mb-1">{{ $summary['total_hari_kerja'] }}</div>
                    <small>Total Hari Kerja</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm" style="background: #10b981; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="fs-3 fw-bold mb-1">{{ $summary['total_hadir'] }}</div>
                    <small>Total Hadir</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm" style="background: #f59e0b; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="fs-3 fw-bold mb-1">{{ $summary['total_terlambat'] }}</div>
                    <small>Total Terlambat</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm" style="background: #3b82f6; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="fs-3 fw-bold mb-1">{{ $summary['total_izin'] }}</div>
                    <small>Total Izin</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm" style="background: #8b5cf6; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="fs-3 fw-bold mb-1">{{ $summary['total_sakit'] }}</div>
                    <small>Total Sakit</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm" style="background: #ef4444; border-radius: 12px;">
                <div class="card-body text-white text-center py-3">
                    <div class="fs-3 fw-bold mb-1">{{ $summary['total_alpa'] }}</div>
                    <small>Total Alpa</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Guru Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px;">
                <div class="card-header" style="background: #f9fafb; border-bottom: 1px solid #e5e7eb; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #374151; font-weight: bold;">Detail Kehadiran Per Guru/Staff</h5>
                </div>
                <div class="card-body p-0" id="dataContainer">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: #f3f4f6;">
                                <tr>
                                    <th style="color: #374151; font-weight: bold;">No.</th>
                                    <th style="color: #374151; font-weight: bold;">Nama Guru</th>
                                    <th style="color: #374151; font-weight: bold;">Jabatan</th>
                                    <th style="color: #374151; font-weight: bold; text-align: center;">Hadir</th>
                                    <th style="color: #374151; font-weight: bold; text-align: center;">Terlambat</th>
                                    <th style="color: #374151; font-weight: bold; text-align: center;">Izin</th>
                                    <th style="color: #374151; font-weight: bold; text-align: center;">Sakit</th>
                                    <th style="color: #374151; font-weight: bold; text-align: center;">Alpa</th>
                                    <th style="color: #374151; font-weight: bold; text-align: center;">Persentase</th>
                                    <th style="color: #374151; font-weight: bold; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['detail_guru'] as $index => $detail)
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="vertical-align: middle;">
                                        <span class="badge rounded-pill" style="background: #64b6f7; color: white;">{{ $index + 1 }}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 35px; height: 35px; background: #64b6f7; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($detail['guru']->nama, 0, 1)) }}
                                            </div>
                                            <strong style="color: #374151;">{{ $detail['guru']->nama }}</strong>
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle; color: #6b7280;">{{ $detail['guru']->jabatan }}</td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <span style="background: #10b981; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ $detail['hadir'] }}
                                        </span>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <span style="background: #f59e0b; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ $detail['terlambat'] }}
                                        </span>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <span style="background: #3b82f6; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ $detail['izin'] }}
                                        </span>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <span style="background: #8b5cf6; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ $detail['sakit'] }}
                                        </span>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <span style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ $detail['alpa'] }}
                                        </span>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <strong style="color: #64b6f7; font-size: 14px;">{{ round($detail['persentase'], 1) }}%</strong>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <span class="badge rounded-pill" style="background: {{ $detail['status'] == 'Baik' ? '#10b981' : ($detail['status'] == 'Cukup' ? '#f59e0b' : '#ef4444') }}; color: white;">
                                            {{ $detail['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #64b6f7; border-radius: 12px; color: white;">
                <div class="card-body p-3">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="fs-5 fw-bold">{{ $summary['total_guru'] }}</div>
                            <small style="opacity: 0.9;">Total Guru/Staff</small>
                        </div>
                        <div class="col-md-3">
                            <div class="fs-5 fw-bold">{{ round($summary['persentase_kehadiran'], 1) }}%</div>
                            <small style="opacity: 0.9;">Persentase Kehadiran Keseluruhan</small>
                        </div>
                        <div class="col-md-3">
                            <div class="fs-5 fw-bold">{{ \Carbon\Carbon::parse($start_date)->diffInDays(\Carbon\Carbon::parse($end_date)) + 1 }}</div>
                            <small style="opacity: 0.9;">Total Hari</small>
                        </div>
                        <div class="col-md-3">
                            <div class="fs-5 fw-bold">{{ now()->format('d M Y') }}</div>
                            <small style="opacity: 0.9;">Tanggal Laporan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f3f4f6 !important;
        transition: background-color 0.2s ease;
    }

    @media print {
        .card {
            border: 1px solid #999 !important;
        }
    }
</style>

<script>
    function setThisMonth() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        
        document.getElementById('start_date').value = formatDate(firstDay);
        document.getElementById('end_date').value = formatDate(now);
    }

    function setThisWeek() {
        const now = new Date();
        const firstDay = new Date(now.getDate() - now.getDay());
        
        document.getElementById('start_date').value = formatDate(firstDay);
        document.getElementById('end_date').value = formatDate(now);
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
</script>
@endsection
