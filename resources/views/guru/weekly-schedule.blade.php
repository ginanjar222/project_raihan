@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="card mb-4 shadow-sm" style="background: linear-gradient(135deg, #64b6f7 0%, #007acc 100%); border: none; border-radius: 12px;">
        <div class="card-body text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="font-weight: 700; font-size: 2rem; font-family: 'DM Sans', sans-serif;">
                        <i class="fas fa-user-plus me-3"></i>Tambah Data Guru & Staff
                    </h1>
                    <p class="mb-0" style="font-family: 'DM Sans', sans-serif; opacity: 0.9;">
                        Sistem Absensi Guru & Staff - SMA YKBBB LELES
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card mb-4 shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
        <div class="card-body p-0">
            <div class="d-flex">
                <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-outline-primary flex-fill" style="border-radius: 12px 0 0 12px; border-right: none; padding: 1rem; font-family: 'DM Sans', sans-serif; font-weight: 500;">
                    <i class="fas fa-user me-2"></i>Data Umum
                </a>
                <button class="btn btn-primary flex-fill" style="border-radius: 0 12px 12px 0; padding: 1rem; font-family: 'DM Sans', sans-serif; font-weight: 500; background: #64b6f7; border-color: #64b6f7;">
                    <i class="fas fa-calendar-week me-2"></i>Jadwal Mingguan
                </button>
            </div>
        </div>
    </div>

    <!-- Info Notice -->
    <div class="alert alert-info shadow-sm mb-4" style="border: none; border-radius: 8px; background: rgba(100, 182, 247, 0.1); border-left: 4px solid #64b6f7;">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle me-3 mt-1" style="color: #64b6f7; font-size: 1.2rem;"></i>
            <div>
                <strong style="color: #1e40af; font-family: 'DM Sans', sans-serif;">Petunjuk:</strong>
                <span style="color: #1e40af; font-family: 'DM Sans', sans-serif;">
                    Atur jadwal kerja untuk setiap hari dalam seminggu. Hari yang diset "Libur" tidak akan dihitung dalam laporan absensi.
                </span>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4" style="border: none; border-radius: 8px; background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2" style="color: #10b981;"></i>
            <span style="color: #047857; font-family: 'DM Sans', sans-serif;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Weekly Schedule Form -->
    <form action="{{ route('guru.weekly-schedule.update', $guru->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #64b6f7; color: white;">
                            <tr>
                                <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Hari
                                </th>
                                <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Status
                                </th>
                                <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Jam Masuk
                                </th>
                                <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Jam Pulang
                                </th>
                                <th class="px-4 py-4" style="border: none; font-weight: 600; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $days = [
                                    'senin' => 'Senin',
                                    'selasa' => 'Selasa', 
                                    'rabu' => 'Rabu',
                                    'kamis' => 'Kamis',
                                    'jumat' => 'Jumat',
                                    'sabtu' => 'Sabtu',
                                    'minggu' => 'Minggu'
                                ];
                            @endphp
                            
                            @foreach($days as $dayKey => $dayName)
                                @php
                                    $schedule = $guru->weeklySchedules->where('hari', $dayKey)->first();
                                    $isActive = $schedule ? $schedule->is_active : in_array($dayKey, ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
                                    $jamMasuk = $schedule ? $schedule->jam_masuk_formatted : '07:00';
                                    $jamPulang = $schedule ? $schedule->jam_pulang_formatted : '15:30';
                                @endphp
                                
                                <tr style="transition: all 0.2s ease; border-bottom: 1px solid #f3f4f6;">
                                    <td class="px-4 py-3 align-middle">
                                        <span style="font-family: 'DM Sans', sans-serif; color: #374151; font-weight: 500; font-size: 1rem;">
                                            {{ $dayName }}
                                        </span>
                                        <input type="hidden" name="schedules[{{ $loop->index }}][hari]" value="{{ $dayKey }}">
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input schedule-toggle" type="checkbox" 
                                                   name="schedules[{{ $loop->index }}][is_active]" 
                                                   id="toggle_{{ $dayKey }}"
                                                   {{ $isActive ? 'checked' : '' }}
                                                   style="width: 3rem; height: 1.5rem; background-color: {{ $isActive ? '#10b981' : '#d1d5db' }}; border: none; border-radius: 1rem;">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock me-2" style="color: #64b6f7;"></i>
                                            <input type="time" 
                                                   name="schedules[{{ $loop->index }}][jam_masuk]" 
                                                   class="form-control time-input" 
                                                   value="{{ $jamMasuk }}"
                                                   style="border: 2px solid #e5e7eb; border-radius: 8px; width: 120px; font-family: 'DM Sans', sans-serif;">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock me-2" style="color: #ef4444;"></i>
                                            <input type="time" 
                                                   name="schedules[{{ $loop->index }}][jam_pulang]" 
                                                   class="form-control time-input" 
                                                   value="{{ $jamPulang }}"
                                                   style="border: 2px solid #e5e7eb; border-radius: 8px; width: 120px; font-family: 'DM Sans', sans-serif;">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <span class="badge status-badge" 
                                              data-active="{{ $isActive ? 'true' : 'false' }}"
                                              style="padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-family: 'DM Sans', sans-serif; font-weight: 500; background: {{ $isActive ? '#10b981' : '#6b7280' }}; color: white;">
                                            {{ $isActive ? 'Aktif' : 'Libur' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Notice Bottom -->
        <div class="alert alert-info shadow-sm mt-4 mb-4" style="border: none; border-radius: 8px; background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6;">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle me-3 mt-1" style="color: #3b82f6; font-size: 1.2rem;"></i>
                <div>
                    <strong style="color: #1e40af; font-family: 'DM Sans', sans-serif;">Catatan:</strong>
                    <span style="color: #1e40af; font-family: 'DM Sans', sans-serif;">
                        Jika status diatur ke "Libur", guru tidak akan bisa melakukan absensi pada hari tersebut.
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('guru.index') }}" class="btn btn-secondary" style="background: #6b7280; border: none; border-radius: 8px; padding: 12px 24px; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button type="submit" class="btn btn-success" style="background: #10b981; border: none; border-radius: 8px; padding: 12px 24px; font-family: 'DM Sans', sans-serif; font-weight: 600;">
                <i class="fas fa-save me-2"></i>Simpan Data Guru
            </button>
        </div>
    </form>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(100, 182, 247, 0.05) !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(100, 182, 247, 0.1);
}

.form-check-input:checked {
    background-color: #10b981 !important;
    border-color: #10b981 !important;
}

.form-check-input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
}

.time-input:focus {
    border-color: #64b6f7;
    box-shadow: 0 0 0 0.2rem rgba(100, 182, 247, 0.25);
    outline: none;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.status-badge {
    transition: all 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle toggle switches
    document.querySelectorAll('.schedule-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const row = this.closest('tr');
            const statusBadge = row.querySelector('.status-badge');
            const timeInputs = row.querySelectorAll('.time-input');
            
            if (this.checked) {
                statusBadge.textContent = 'Aktif';
                statusBadge.style.background = '#10b981';
                statusBadge.setAttribute('data-active', 'true');
                this.style.backgroundColor = '#10b981';
                
                // Enable time inputs
                timeInputs.forEach(input => {
                    input.disabled = false;
                    input.style.opacity = '1';
                });
            } else {
                statusBadge.textContent = 'Libur';
                statusBadge.style.background = '#6b7280';
                statusBadge.setAttribute('data-active', 'false');
                this.style.backgroundColor = '#d1d5db';
                
                // Keep time inputs enabled but with visual indication
                timeInputs.forEach(input => {
                    input.disabled = false;
                    input.style.opacity = '0.6';
                });
            }
        });
        
        // Initialize toggle appearance
        if (toggle.checked) {
            toggle.style.backgroundColor = '#10b981';
        } else {
            toggle.style.backgroundColor = '#d1d5db';
        }
    });
});
</script>
@endsection
