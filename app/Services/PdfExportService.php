<?php

namespace App\Services;

use Carbon\Carbon;

class PdfExportService
{
    /**
     * Generate HTML untuk rekap mingguan
     */
    public static function generateMingguanPdfHtml($data, $start, $end)
    {
        $dayMapping = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            7 => 'minggu'
        ];

        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Mingguan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 10px; 
            color: #333;
            line-height: 1.2;
            background: white;
            padding: 20px;
        }
        @page { 
            size: A4 landscape;
            margin: 8mm;
        }
        @media print {
            body { margin: 0; padding: 10mm; }
            .no-print { display: none; }
        }
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }
        .download-section {
            margin-bottom: 20px;
            text-align: center;
        }
        .download-btn {
            background: #007acc;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin: 0 5px;
        }
        .download-btn:hover {
            background: #005a9e;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header-school {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .header-title {
            font-weight: bold;
            font-size: 12px;
            margin: 3px 0;
        }
        .header-subtitle {
            font-size: 10px;
            margin: 2px 0;
        }
        .header-period {
            font-size: 9px;
            color: #555;
            margin: 2px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #1a4d7a;
            color: white;
            font-weight: bold;
            vertical-align: middle;
        }
        td.left { text-align: left; padding-left: 5px; }
        .day-column {
            width: 3%;
            font-weight: bold;
            font-size: 8px;
        }
        .day-number {
            font-size: 7px;
        }
        .day-letter {
            font-size: 8px;
            font-weight: bold;
        }
        .keterangan {
            margin-top: 10px;
            font-size: 8px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .signature-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-around;
            font-size: 8px;
        }
        .signature-box {
            text-align: center;
            width: 22%;
        }
        .signature-label {
            margin-bottom: 25px;
            font-weight: bold;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            margin-bottom: 2px;
            height: 15px;
        }
        .signature-name {
            font-size: 8px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #999;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Download buttons section -->
    <div class="download-section no-print">
        <button class="download-btn" onclick="window.print()">🖨 Print / Save as PDF</button>
        <button class="download-btn" onclick="downloadHTML()">📥 Download HTML</button>
    </div>';

        // Header
        $html .= '<div class="header">
            <div class="header-school">ABSENSI GURU SEKOLAH</div>
            <div class="header-title">SMA YKBBB LELES</div>
            <div class="header-subtitle">Semester Genap Tahun Pelajaran 2024-2025</div>
            <div class="header-period">Website: www.belajaroffice.com</div>
            <div class="info-row">
                <div>Periode: ' . $start->format('d F Y') . ' s/d ' . $end->format('d F Y') . '</div>
                <div>Tanggal Cetak: ' . Carbon::now()->format('d-m-Y H:i') . '</div>
            </div>
        </div>';

        // Generate kolom hari
        $dayColumns = [];
        $current = $start->copy();
        
        while ($current <= $end) {
            $dayColumns[] = [
                'date' => $current->format('Y-m-d'),
                'day_num' => $current->day,
                'day_letter' => strtoupper(substr($current->format('D'), 0, 1))
            ];
            $current->addDay();
        }

        // Tabel header
        $html .= '<table>
            <thead>
                <tr>
                    <th style="width: 3%">No</th>
                    <th style="width: 12%">NIPTK</th>
                    <th style="width: 22%">Nama</th>
                    <th style="width: 12%">Bidang Studi</th>';

        foreach ($dayColumns as $col) {
            $html .= '<th class="day-column">
                <div class="day-number">' . $col['day_num'] . '</div>
                <div class="day-letter">' . $col['day_letter'] . '</div>
            </th>';
        }

        $html .= '<th style="width: 3%">H</th>
                    <th style="width: 3%">T</th>
                    <th style="width: 3%">I</th>
                    <th style="width: 3%">S</th>
                    <th style="width: 3%">A</th>
                </tr>
            </thead>
            <tbody>';

        $no = 1;
        $totals = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

        if ($data->isEmpty()) {
            $html .= '<tr>
                <td colspan="' . (4 + count($dayColumns) + 5) . '" class="no-data">
                    Tidak ada data guru untuk periode ini
                </td>
            </tr>';
        } else {
            foreach ($data as $guru) {
                $guruStats = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

                $html .= '<tr>
                    <td>' . $no++ . '</td>
                    <td class="left">' . ($guru->uid ?? '-') . '</td>
                    <td class="left">' . $guru->nama . '</td>
                    <td>' . ($guru->jabatan ?? '-') . '</td>';

                // Loop untuk setiap hari
                foreach ($dayColumns as $col) {
                    $absensi = $guru->absensi->firstWhere('tanggal', $col['date']);
                    
                    if ($absensi) {
                        $status = strtoupper(substr($absensi->status_masuk, 0, 1));
                        $html .= '<td>' . $status . '</td>';
                        
                        // Count status
                        if ($absensi->status_masuk === 'hadir') $guruStats['hadir']++;
                        elseif ($absensi->status_masuk === 'terlambat') $guruStats['terlambat']++;
                        elseif ($absensi->status_masuk === 'izin') $guruStats['izin']++;
                        elseif ($absensi->status_masuk === 'sakit') $guruStats['sakit']++;
                        elseif ($absensi->status_masuk === 'alpa') $guruStats['alpa']++;
                    } else {
                        $html .= '<td>-</td>';
                    }
                }

                // Total per guru
                $html .= '<td>' . $guruStats['hadir'] . '</td>
                        <td>' . $guruStats['terlambat'] . '</td>
                        <td>' . $guruStats['izin'] . '</td>
                        <td>' . $guruStats['sakit'] . '</td>
                        <td>' . $guruStats['alpa'] . '</td>
                    </tr>';

                // Akumulasi total
                foreach ($guruStats as $key => $value) {
                    $totals[$key] += $value;
                }
            }
        }

        $html .= '</tbody>
        </table>';

        // Keterangan
        $html .= '<div class="keterangan">
            <strong>Keterangan:</strong> H = Hadir, T = Terlambat, I = Izin, S = Sakit, A = Alpa
        </div>';

        // Tanda tangan
        $html .= '<div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Mengetahui</div>
                <div>Kepala Sekolah</div>
                <div class="signature-line"></div>
                <div class="signature-name">(...........................)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Dibuat Oleh</div>
                <div>Petugas Absensi</div>
                <div class="signature-line"></div>
                <div class="signature-name">(...........................)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Diketahui</div>
                <div>Wakil Kepala Sekolah</div>
                <div class="signature-line"></div>
                <div class="signature-name">(...........................)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Banjarmasin</div>
                <div style="margin-top: 25px;">' . Carbon::now()->format('d F Y') . '</div>
            </div>
        </div>';

        $html .= '
    <script>
        function downloadHTML() {
            const blob = new Blob([document.documentElement.innerHTML], { type: "text/html" });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "Rekap-Absensi-Mingguan-' . $start->format('d-m-Y') . '_' . $end->format('d-m-Y') . '.html";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    </script>
</div>
</body>
</html>';

        return $html;
    }

    /**
     * Generate HTML untuk rekap bulanan
     */
    public static function generateBulanianPdfHtml($data, $start, $end)
    {
        $dayMapping = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            7 => 'minggu'
        ];

        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Bulanan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 11px; 
            color: #333;
            line-height: 1.3;
            background: white;
            padding: 20px;
        }
        @page { 
            size: A4 portrait;
            margin: 10mm;
        }
        @media print {
            body { margin: 0; padding: 10mm; }
            .no-print { display: none; }
        }
        .container {
            width: 100%;
        }
        .download-section {
            margin-bottom: 20px;
            text-align: center;
        }
        .download-btn {
            background: #007acc;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin: 0 5px;
        }
        .download-btn:hover {
            background: #005a9e;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header-school {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .header-title {
            font-weight: bold;
            font-size: 12px;
            margin: 3px 0;
        }
        .header-period {
            font-size: 10px;
            color: #555;
            margin-top: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #1a4d7a;
            color: white;
            font-weight: bold;
        }
        td.left { text-align: left; padding-left: 8px; }
        .summary-table {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .summary-table td {
            padding: 6px;
        }
        .summary-label { 
            font-weight: bold;
            text-align: left;
            width: 50%;
        }
        .status-baik { color: green; font-weight: bold; }
        .status-cukup { color: orange; font-weight: bold; }
        .status-kurang { color: red; font-weight: bold; }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-label {
            margin-bottom: 25px;
            font-weight: bold;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            margin-bottom: 3px;
            height: 15px;
        }
        .signature-name {
            font-size: 9px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #999;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Download buttons section -->
    <div class="download-section no-print">
        <button class="download-btn" onclick="window.print()">🖨 Print / Save as PDF</button>
        <button class="download-btn" onclick="downloadHTML()">📥 Download HTML</button>
    </div>';

        // Header
        $html .= '<div class="header">
            <div class="header-school">ABSENSI GURU SEKOLAH</div>
            <div class="header-title">SMA YKBBB LELES</div>
            <div class="header-title">LAPORAN REKAP ABSENSI BULANAN</div>
            <div class="header-period">Periode: ' . $start->format('d F Y') . ' s/d ' . $end->format('d F Y') . '</div>
            <div class="info-row">
                <div></div>
                <div>Tanggal Cetak: ' . Carbon::now()->format('d-m-Y H:i') . '</div>
            </div>
        </div>';

        // Tabel detail
        $html .= '<table>
            <thead>
                <tr>
                    <th style="width: 5%">No.</th>
                    <th style="width: 20%">Nama Guru/Staff</th>
                    <th style="width: 15%">Jabatan</th>
                    <th style="width: 8%">Hari Kerja</th>
                    <th style="width: 8%">Hadir</th>
                    <th style="width: 10%">Terlambat</th>
                    <th style="width: 8%">Izin</th>
                    <th style="width: 8%">Sakit</th>
                    <th style="width: 8%">Alpa</th>
                    <th style="width: 7%">%</th>
                    <th style="width: 8%">Status</th>
                </tr>
            </thead>
            <tbody>';

        $no = 1;
        $totals = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        $totalWorkDays = 0;

        if ($data->isEmpty()) {
            $html .= '<tr>
                <td colspan="11" class="no-data">Tidak ada data guru untuk periode ini</td>
            </tr>';
        } else {
            foreach ($data as $guru) {
                $workDays = 0;
                $current = $start->copy();

                while ($current <= $end) {
                    $dayOfWeek = $current->dayOfWeek == 0 ? 7 : $current->dayOfWeek;
                    $hariIndonesia = $dayMapping[$dayOfWeek];
                    $schedule = $guru->weeklySchedules->firstWhere('hari', $hariIndonesia);
                    
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

                $totalKehadiran = $hadir + $terlambat;
                $percentage = $workDays > 0 ? round(($totalKehadiran / $workDays) * 100, 1) : 0;
                $status = $percentage >= 80 ? 'Baik' : ($percentage >= 60 ? 'Cukup' : 'Kurang');
                $statusClass = $percentage >= 80 ? 'status-baik' : ($percentage >= 60 ? 'status-cukup' : 'status-kurang');

                $html .= '<tr>
                    <td>' . $no++ . '</td>
                    <td class="left">' . $guru->nama . '</td>
                    <td>' . ($guru->jabatan ?? '-') . '</td>
                    <td>' . $workDays . '</td>
                    <td>' . $hadir . '</td>
                    <td>' . $terlambat . '</td>
                    <td>' . $izin . '</td>
                    <td>' . $sakit . '</td>
                    <td>' . $alpa . '</td>
                    <td>' . $percentage . '%</td>
                    <td class="' . $statusClass . '">' . $status . '</td>
                </tr>';

                $totals['hadir'] += $hadir;
                $totals['terlambat'] += $terlambat;
                $totals['izin'] += $izin;
                $totals['sakit'] += $sakit;
                $totals['alpa'] += $alpa;
                $totalWorkDays += $workDays;
            }
        }

        $html .= '</tbody>
        </table>';

        // Ringkasan
        $totalKehadiranKeseluruhan = $totals['hadir'] + $totals['terlambat'];
        $percentageKeseluruhan = $totalWorkDays > 0 ? round(($totalKehadiranKeseluruhan / $totalWorkDays) * 100, 1) : 0;

        $html .= '<div class="summary-table">
            <table>
                <tr>
                    <td class="summary-label">Total Guru/Staff</td>
                    <td>' . $data->count() . '</td>
                </tr>
                <tr>
                    <td class="summary-label">Total Hari Kerja</td>
                    <td>' . $totalWorkDays . '</td>
                </tr>
                <tr>
                    <td class="summary-label">Total Hadir</td>
                    <td>' . $totals['hadir'] . '</td>
                </tr>
                <tr>
                    <td class="summary-label">Total Terlambat</td>
                    <td>' . $totals['terlambat'] . '</td>
                </tr>
                <tr>
                    <td class="summary-label">Total Izin</td>
                    <td>' . $totals['izin'] . '</td>
                </tr>
                <tr>
                    <td class="summary-label">Total Sakit</td>
                    <td>' . $totals['sakit'] . '</td>
                </tr>
                <tr>
                    <td class="summary-label">Total Alpa</td>
                    <td>' . $totals['alpa'] . '</td>
                </tr>
                <tr>
                    <td class="summary-label"><strong>Persentase Kehadiran</strong></td>
                    <td><strong>' . $percentageKeseluruhan . '%</strong></td>
                </tr>
            </table>
        </div>';

        // Tanda tangan
        $html .= '<div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Mengetahui</div>
                <div>Kepala Sekolah</div>
                <div class="signature-line"></div>
                <div class="signature-name">(...........................)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Dibuat Oleh</div>
                <div>Petugas Absensi</div>
                <div class="signature-line"></div>
                <div class="signature-name">(...........................)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Banjarmasin</div>
                <div style="margin-top: 25px;">' . Carbon::now()->format('d F Y') . '</div>
            </div>
        </div>';

        $html .= '
    <script>
        function downloadHTML() {
            const blob = new Blob([document.documentElement.innerHTML], { type: "text/html" });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "Rekap-Absensi-Bulanan-' . $start->format('F-Y') . '.html";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    </script>
</div>
</body>
</html>';

        return $html;
    }
}
