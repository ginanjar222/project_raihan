@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header yang lebih profesional dan clean -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="welcome-header">
                <h1 class="display-6 fw-bold text-dark mb-1">Dashboard Presensi</h1>
                <p class="text-muted mb-0 fs-6">Sistem Monitoring Kehadiran Guru & Staff</p>
            </div>
        </div>
    </div>

    <!-- Stats cards utama -->
    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-primary">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $totalGuru }}">0</div>
                    <div class="stats-label">Total Guru/Staff</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-success">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $sudahAbsen }}">0</div>
                    <div class="stats-label">Sudah Presensi Hari Ini</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-warning">
                <div class="stats-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $belumAbsen }}">0</div>
                    <div class="stats-label">Belum Presensi Hari Ini</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-info">
                <div class="stats-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" id="digitalClock">00:00:00</div>
                    <div class="stats-label">Waktu Real-time</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menambahkan statistik detail status kehadiran -->
    <div class="row g-3 mb-2">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card stats-success-light">
                <div class="stats-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $hadir ?? 0 }}" id="hadir">0</div>
                    <div class="stats-label">Hadir</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card stats-warning">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $terlambat ?? 0 }}" id="terlambat">0</div>
                    <div class="stats-label">Terlambat</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card stats-info">
                <div class="stats-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $izin ?? 0 }}" id="izin">0</div>
                    <div class="stats-label">Izin</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card stats-secondary">
                <div class="stats-icon">
                    <i class="fas fa-thermometer-half"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $sakit ?? 0 }}" id="sakit">0</div>
                    <div class="stats-label">Sakit</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card stats-danger">
                <div class="stats-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $alpa ?? 0 }}" id="alpa">0</div>
                    <div class="stats-label">Alpa</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card stats-primary">
                <div class="stats-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number" data-target="{{ $masihDiSekolah ?? 0 }}" id="masih-sekolah">0</div>
                    <div class="stats-label">Masih di Sekolah</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart container yang lebih profesional -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Status Kehadiran Hari Ini
                    </h4>
                    <div class="chart-subtitle">
                        {{ date('d F Y') }} - Kehadiran Efektif: {{ $totalGuru > 0 ? round((($hadir ?? 0) + ($terlambat ?? 0)) / $totalGuru * 100) : 0 }}%
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-wrapper">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color bg-success"></div>
                            <span>Hadir ({{ $hadir ?? 0 }})</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-warning"></div>
                            <span>Terlambat ({{ $terlambat ?? 0 }})</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-info"></div>
                            <span>Izin ({{ $izin ?? 0 }})</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-secondary"></div>
                            <span>Sakit ({{ $sakit ?? 0 }})</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-danger"></div>
                            <span>Alpa ({{ $alpa ?? 0 }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Ringkasan Kehadiran
                    </h4>
                    <div class="chart-subtitle">
                        Total: {{ $totalGuru }} Guru/Staff
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-wrapper">
                        <canvas id="summaryChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color bg-success"></div>
                            <span>Sudah Absen ({{ $sudahAbsen }})</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-warning"></div>
                            <span>Belum Absen ({{ $belumAbsen }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Welcome header yang lebih profesional */
.welcome-header {
    text-align: center;
    padding: 0.8rem 0; /* mengurangi padding dari 1.5rem menjadi 0.8rem */
    animation: fadeInDown 0.6s ease-out;
}

.text-muted {
    color: #6b7280 !important;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stats cards yang lebih clean dan profesional */
.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.2rem; /* mengurangi padding dari 2rem menjadi 1.2rem */
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    animation: slideInUp 0.5s ease-out;
    height: 100px; /* mengurangi height dari 140px menjadi 100px */
    box-shadow: 0 4px 20px rgba(30, 58, 138, 0.08);
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(30, 58, 138, 0.15);
}

.stats-card:nth-child(1) { animation-delay: 0.1s; }
.stats-card:nth-child(2) { animation-delay: 0.2s; }
.stats-card:nth-child(3) { animation-delay: 0.3s; }
.stats-card:nth-child(4) { animation-delay: 0.4s; }
.stats-card:nth-child(5) { animation-delay: 0.5s; }
.stats-card:nth-child(6) { animation-delay: 0.6s; }

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-icon {
    position: absolute;
    top: 1rem; /* mengurangi dari 1.5rem menjadi 1rem */
    right: 1rem; /* mengurangi dari 1.5rem menjadi 1rem */
    font-size: 1.8rem; /* mengurangi dari 2.2rem menjadi 1.8rem */
    opacity: 0.15;
    transition: all 0.3s ease;
}

.stats-card:hover .stats-icon {
    opacity: 0.25;
    transform: scale(1.05);
}

.stats-content {
    position: relative;
    z-index: 2;
}

.stats-number {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.8rem; /* mengurangi dari 2.2rem menjadi 1.8rem */
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.3rem; /* mengurangi dari 0.5rem menjadi 0.3rem */
    line-height: 1;
}

.stats-label {
    color: #6b7280;
    font-size: 0.8rem; /* mengurangi dari 0.9rem menjadi 0.8rem */
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Color variants yang lebih profesional */
.stats-primary { border-left: 4px solid #1e3a8a; }
.stats-success { border-left: 4px solid #059669; }
.stats-warning { border-left: 4px solid #d97706; }
.stats-info { border-left: 4px solid #0891b2; }
.stats-danger { border-left: 4px solid #dc2626; }
.stats-secondary { border-left: 4px solid #6b7280; }
.stats-success-light { border-left: 4px solid #10b981; }

.stats-primary .stats-icon { color: #1e3a8a; }
.stats-success .stats-icon { color: #059669; }
.stats-warning .stats-icon { color: #d97706; }
.stats-info .stats-icon { color: #0891b2; }
.stats-danger .stats-icon { color: #dc2626; }
.stats-secondary .stats-icon { color: #6b7280; }
.stats-success-light .stats-icon { color: #10b981; }

/* Chart container yang lebih elegant */
.chart-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.2rem; /* mengurangi padding dari 2rem menjadi 1.2rem */
    animation: fadeInUp 0.6s ease-out 0.4s both;
    box-shadow: 0 4px 20px rgba(30, 58, 138, 0.08);
}

.chart-header {
    text-align: center;
    margin-bottom: 1rem; /* mengurangi dari 2rem menjadi 1rem */
}

.chart-title {
    font-family: 'Space Grotesk', sans-serif;
    color: #1f2937;
    font-weight: 600;
    margin-bottom: 0.3rem; /* mengurangi dari 0.5rem menjadi 0.3rem */
    font-size: 1.1rem; /* mengurangi dari 1.3rem menjadi 1.1rem */
}

.chart-subtitle {
    color: #6b7280;
    font-size: 0.85rem; /* mengurangi dari 0.95rem menjadi 0.85rem */
    font-weight: 500;
}

.chart-wrapper {
    max-width: 180px; /* mengurangi dari 250px menjadi 180px */
    height: 140px; /* mengurangi dari 200px menjadi 140px */
    margin: 0 auto 1rem; /* mengurangi margin bottom dari 2rem menjadi 1rem */
    position: relative;
}

.chart-legend {
    display: flex;
    justify-content: center;
    gap: 1.5rem; /* mengurangi dari 2rem menjadi 1.5rem */
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem; /* mengurangi dari 0.75rem menjadi 0.5rem */
    color: #4b5563;
    font-weight: 500;
    font-size: 0.85rem; /* mengurangi dari 0.95rem menjadi 0.85rem */
}

.legend-color {
    width: 14px; /* mengurangi dari 16px menjadi 14px */
    height: 14px; /* mengurangi dari 16px menjadi 14px */
    border-radius: 50%;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive design yang lebih baik */
@media (max-width: 768px) {
    .stats-card {
        padding: 1rem; /* mengurangi dari 1.5rem menjadi 1rem */
        height: 85px; /* mengurangi dari 120px menjadi 85px */
    }
    
    .stats-number {
        font-size: 1.5rem; /* mengurangi dari 1.8rem menjadi 1.5rem */
    }
    
    .chart-legend {
        gap: 1rem; /* mengurangi dari 1.5rem menjadi 1rem */
    }
    
    .chart-wrapper {
        max-width: 150px; /* mengurangi dari 200px menjadi 150px */
        height: 120px; /* mengurangi dari 160px menjadi 120px */
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw: function(chart) {
            if (chart.config.options.plugins.centerText && chart.config.options.plugins.centerText.display) {
                const ctx = chart.ctx;
                const centerConfig = chart.config.options.plugins.centerText;
                const fontStyle = centerConfig.fontStyle || 'Arial';
                const txt = centerConfig.text;
                const color = centerConfig.color || '#000';
                const sidePadding = centerConfig.sidePadding || 20;
                const sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2);

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                const centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                const centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);

                const stringWidth = ctx.measureText(txt).width;
                const elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

                const widthRatio = elementWidth / stringWidth;
                const newFontSize = Math.floor(30 * widthRatio);
                const fontSizeToUse = Math.min(newFontSize, 28);

                ctx.font = fontSizeToUse + "px " + fontStyle;
                ctx.fillStyle = color;
                ctx.fillText(txt, centerX, centerY);
            }
        }
    };

    Chart.register(centerTextPlugin);

    function updateDigitalClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const clockElement = document.getElementById('digitalClock');
        const newTime = `${hours}:${minutes}:${seconds}`;
        
        if (clockElement.textContent !== newTime) {
            clockElement.style.transform = 'scale(1.05)';
            setTimeout(() => {
                clockElement.textContent = newTime;
                clockElement.style.transform = 'scale(1)';
            }, 100);
        }
    }
    
    setInterval(updateDigitalClock, 1000);
    updateDigitalClock();

    function animateNumbers() {
        const numbers = document.querySelectorAll('.stats-number[data-target]');
        
        numbers.forEach(number => {
            const target = parseInt(number.getAttribute('data-target'));
            const duration = 1500;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                number.textContent = Math.floor(current);
            }, 16);
        });
    }

    function updateChartLegends(data) {
        // Update legend untuk status chart
        const statusLegendItems = document.querySelectorAll('.chart-container:first-child .legend-item span');
        if (statusLegendItems.length >= 5) {
            statusLegendItems[0].textContent = `Hadir (${data.hadir || 0})`;
            statusLegendItems[1].textContent = `Terlambat (${data.terlambat || 0})`;
            statusLegendItems[2].textContent = `Izin (${data.izin || 0})`;
            statusLegendItems[3].textContent = `Sakit (${data.sakit || 0})`;
            statusLegendItems[4].textContent = `Alpa (${data.alpa || 0})`;
        }

        // Mengupdate legend untuk summary chart dengan data sudah absen/belum absen
        const summaryLegendItems = document.querySelectorAll('.chart-container:last-child .legend-item span');
        if (summaryLegendItems.length >= 2) {
            summaryLegendItems[0].textContent = `Sudah Absen (${data.sudahAbsen || 0})`;
            summaryLegendItems[1].textContent = `Belum Absen (${data.belumAbsen || 0})`;
        }
    }

    function updateStatCard(type, value) {
        const selectors = {
            'hadir': '#hadir',
            'terlambat': '#terlambat',
            'izin': '#izin',
            'sakit': '#sakit',
            'alpa': '#alpa',
            // Memperbaiki selector untuk card sudah absen dan belum absen
            'sudah-absen': '.stats-success .stats-number',
            'belum-absen': '.stats-warning .stats-number',
            'masih-sekolah': '#masih-sekolah'
        };
        
        const element = document.querySelector(selectors[type]);
        if (element) {
            const currentValue = parseInt(element.textContent) || 0;
            if (currentValue !== value) {
                element.style.transform = 'scale(1.1)';
                element.style.color = '#dc2626';
                setTimeout(() => {
                    element.textContent = value;
                    element.setAttribute('data-target', value);
                    element.style.transform = 'scale(1)';
                    element.style.color = '';
                }, 200);
            }
        }
    }

    const totalGuru = {{ $totalGuru }};
    const kehadiran = {{ $hadir ?? 0 }} + {{ $terlambat ?? 0 }};
    const persentaseKehadiran = totalGuru > 0 ? Math.round((kehadiran / totalGuru) * 100) : 0;

    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'],
            datasets: [{
                data: [{{ $hadir ?? 0 }}, {{ $terlambat ?? 0 }}, {{ $izin ?? 0 }}, {{ $sakit ?? 0 }}, {{ $alpa ?? 0 }}],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(217, 119, 6, 0.8)',
                    'rgba(8, 145, 178, 0.8)',
                    'rgba(107, 114, 128, 0.8)',
                    'rgba(220, 38, 38, 0.8)'
                ],
                borderColor: [
                    'rgba(16, 185, 129, 1)',
                    'rgba(217, 119, 6, 1)',
                    'rgba(8, 145, 178, 1)',
                    'rgba(107, 114, 128, 1)',
                    'rgba(220, 38, 38, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.95)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8
                },
                centerText: {
                    display: true,
                    text: persentaseKehadiran + '%',
                    color: '#1f2937',
                    fontStyle: 'Arial'
                }
            }
        }
    });

    const persentaseAbsen = totalGuru > 0 ? Math.round(({{ $sudahAbsen }} / totalGuru) * 100) : 0;

    const summaryCtx = document.getElementById('summaryChart').getContext('2d');
    const summaryChart = new Chart(summaryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Sudah Absen', 'Belum Absen'],
            datasets: [{
                data: [{{ $sudahAbsen }}, {{ $belumAbsen }}],
                backgroundColor: [
                    'rgba(5, 150, 105, 0.8)',
                    'rgba(217, 119, 6, 0.8)'
                ],
                borderColor: [
                    'rgba(5, 150, 105, 1)',
                    'rgba(217, 119, 6, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.95)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8
                },
                centerText: {
                    display: true,
                    text: persentaseAbsen + '%',
                    color: '#1f2937',
                    fontStyle: 'Arial'
                }
            }
        }
    });

    window.statusChart = statusChart;
    window.summaryChart = summaryChart;

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(animateNumbers, 300);
        setInterval(refreshDashboardStats, 30000);
        setTimeout(refreshDashboardStats, 2000);
    });

    function refreshDashboardStats() {
        fetch('/dashboard/stats', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            updateStatCard('hadir', data.hadir || 0);
            updateStatCard('terlambat', data.terlambat || 0);
            updateStatCard('izin', data.izin || 0);
            updateStatCard('sakit', data.sakit || 0);
            updateStatCard('alpa', data.alpa || 0);
            updateStatCard('sudah-absen', data.sudahAbsen || 0);
            updateStatCard('belum-absen', data.belumAbsen || 0);
            updateStatCard('masih-sekolah', data.masihDiSekolah || 0);
            
            if (window.statusChart) {
                const totalGuru = data.totalGuru || {{ $totalGuru }};
                const kehadiran = (data.hadir || 0) + (data.terlambat || 0);
                const persentaseKehadiran = totalGuru > 0 ? Math.round((kehadiran / totalGuru) * 100) : 0;
                
                window.statusChart.data.datasets[0].data = [
                    data.hadir || 0, 
                    data.terlambat || 0, 
                    data.izin || 0, 
                    data.sakit || 0, 
                    data.alpa || 0
                ];
                window.statusChart.options.plugins.centerText.text = persentaseKehadiran + '%';
                window.statusChart.update();
            }
            
            if (window.summaryChart) {
                const totalGuru = data.totalGuru || {{ $totalGuru }};
                const persentaseAbsen = totalGuru > 0 ? Math.round(((data.sudahAbsen || 0) / totalGuru) * 100) : 0;
                
                // Mengupdate chart kedua dengan data sudah absen vs belum absen
                window.summaryChart.data.datasets[0].data = [data.sudahAbsen || 0, data.belumAbsen || 0];
                window.summaryChart.options.plugins.centerText.text = persentaseAbsen + '%';
                window.summaryChart.update();
            }
            
            updateChartLegends(data);
            
            console.log('Dashboard stats updated:', data);
        })
        .catch(error => {
            console.error('Error refreshing dashboard stats:', error);
        });
    }
</script>
@endsection
