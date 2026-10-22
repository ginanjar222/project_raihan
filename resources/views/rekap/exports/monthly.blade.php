<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Laporan Presensi Guru Bulanan</title>
<style>
  /* ========= BASE ========= */
  *{margin:0;padding:0;box-sizing:border-box}
  html,body{background:#fff;color:#111;font:9px/1.35 "Tahoma","Segoe UI",Arial,sans-serif;-webkit-print-color-adjust:exact;print-color-adjust:exact}
  .tabular{font-variant-numeric:tabular-nums}
  @page{ size:A4 landscape; margin:8mm 8mm 9mm 8mm; }
  @media print{ thead{display:table-header-group} tfoot{display:table-footer-group} }
  .sheet{border:1px solid #000;border-radius:2px;padding:6mm;margin:1mm 0}

  /* ========= HEADER ========= */
  .title{text-align:center;font-weight:800;font-size:11.5px}
  .sub{text-align:center;font-size:8.5px;margin-top:1mm}
  .meta-inline{display:flex;justify-content:space-between;align-items:flex-start;margin:3.5mm 0 2.5mm;font-size:8px}
  .meta-left{white-space:nowrap}
  .meta-right{text-align:right}
  .divider{border-top:1px solid #000;margin:3mm 0}

  /* ========= SUMMARY BAR (horizontal) ========= */
  .summary-bar{margin:2mm 0 3mm}
  .summary-table{width:100%;border-collapse:collapse;font-size:9px}
  .summary-table th,.summary-table td{border:1px solid #000;padding:1.8mm 2mm;text-align:center}
  .summary-table th{background:#efefef;font-weight:800}

  /* ========= TABLE CARD ========= */
  .table-card{border:1px solid #000;border-radius:2px;padding:3.5mm;margin:2mm 0 3mm}
  .table-caption{text-align:center;font-weight:800;margin-bottom:2mm;font-size:9px}

  /* ========= MAIN TABLE ========= */
  table{width:100%;border-collapse:collapse}
  th,td{border:1px solid #000;padding:2.2mm 1.5mm;text-align:center;vertical-align:middle}
  .left{text-align:left;padding-left:6px}
  tbody tr:nth-child(odd){background:#fafafa}
  tr{page-break-inside:avoid}

  /* lebar kolom ringkas (tanpa kolom hari) */
  th[col-no]{width:4%}
  th[col-nuptk]{width:12%}
  th[col-nama]{width:22%}
  th[col-bidang]{width:16%}
  th[col-num]{width:6%}
  th[col-pct]{width:5%}
  th[col-st]{width:9%}

  thead{background:#1a5490;color:#fff}
  thead th{border-color:#16477d}
  .status-good{color:#27ae60;font-weight:800}
  .status-fair{color:#f39c12;font-weight:800}
  .status-poor{color:#e74c3c;font-weight:800}

  /* ========= SIGNATURE (1 baris, rata kanan) ========= */
  .signature-wrap{margin-top:4mm;display:flex;justify-content:flex-end;gap:8mm;align-items:flex-end;font-size:7.5px}
  .sig{text-align:center;min-width:45mm}
  .sig .t{font-weight:700;margin-bottom:.6mm}
  .sig .r{font-size:7px;color:#333;margin-bottom:6mm}
  .sig .name{font-size:7px}

  /* ========= FOOTER ========= */
  .footer{margin-top:3mm;display:flex;justify-content:space-between;align-items:flex-end;font-size:7.6px;color:#333}
  .legend b{font-weight:800}
  .no-data{text-align:center;padding:6mm 0;color:#555;font-style:italic}
</style>
</head>
<body>
@php
  // Rekap bulanan: gunakan rentang $start s.d. $end (awal & akhir bulan)
@endphp

<div class="sheet">
  <!-- HEADER -->
  <div class="title">Laporan Presensi Guru Bulanan</div>
  <div class="sub">SMA YKBBB LELES</div>

  <!-- Bulan/Tahun sebagai teks (bukan tabel) -->
  <div class="meta-inline">
    <div class="meta-left">
      <strong>Bulan:</strong> <span class="tabular">{{ $start->format('m') }}</span>
      &nbsp;|&nbsp;
      <strong>Tahun:</strong> <span class="tabular">{{ $start->format('Y') }}</span>
    </div>
    <div class="meta-right">
      Periode: <strong>{{ $start->format('F Y') }}</strong>
      <small>({{ $start->format('d M Y') }} – {{ $end->format('d M Y') }})</small><br>
      Cetak: {{ now()->format('d/m/Y H:i') }}
    </div>
  </div>

  <div class="divider"></div>

  <!-- SUMMARY (horizontal – sama gaya mingguan) -->
  <div class="summary-bar">
    <table class="summary-table">
      <tr>
        <th>Total Guru</th><td class="tabular">{{ $data->count() }}</td>
        <th>Hadir</th><td class="tabular">{{ $summary['hadir'] ?? 0 }}</td>
        <th>Terlambat</th><td class="tabular">{{ $summary['terlambat'] ?? 0 }}</td>
        <th>Izin</th><td class="tabular">{{ $summary['izin'] ?? 0 }}</td>
        <th>Sakit</th><td class="tabular">{{ $summary['sakit'] ?? 0 }}</td>
        <th>Alpa</th><td class="tabular">{{ $summary['alpa'] ?? 0 }}</td>
      </tr>
    </table>
  </div>

  <!-- TABEL REKAP BULANAN (tanpa kolom hari) -->
  <div class="table-card">
    <div class="table-caption">Rekapitulasi Presensi Bulanan</div>
    <table>
      <thead>
        <tr>
          <th col-no>No</th>
          <th col-nuptk>NUPTK</th>
          <th col-nama>Nama</th>
          <th col-bidang>Bidang Studi</th>
          <th col-num>H.Kerja</th>
          <th col-num>Hadir</th>
          <th col-num>Terlambat</th>
          <th col-num>Izin</th>
          <th col-num>Sakit</th>
          <th col-num>Alpa</th>
          <th col-pct>%</th>
          <th col-st>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $index => $guru)
          @php
            // Hitung hari kerja aktif menurut jadwal mingguan guru di bulan ini
            $workDays = 0;
            $cur = $start->copy();
            $dayMap = [1=>'senin',2=>'selasa',3=>'rabu',4=>'kamis',5=>'jumat',6=>'sabtu',7=>'minggu'];
            
            // Build associative array untuk quick lookup dengan format Y-m-d
            $absensiByDate = [];
            foreach($guru->absensi as $ab) {
              $absensiByDate[$ab->tanggal->format('Y-m-d')] = $ab;
            }
            
            while($cur <= $end){
              $dow = $cur->dayOfWeek == 0 ? 7 : $cur->dayOfWeek;
              $hari = $dayMap[$dow];
              $sch = $guru->weeklySchedules->firstWhere('hari',$hari);
              if($sch && $sch->is_active){ $workDays++; }
              $cur->addDay();
            }

            $hadir     = 0;
            $terlambat = 0;
            $izin      = 0;
            $sakit     = 0;
            $alpa      = 0;
            
            $cur = $start->copy();
            while($cur <= $end){
              $dow = $cur->dayOfWeek == 0 ? 7 : $cur->dayOfWeek;
              $hari = $dayMap[$dow];
              $sch = $guru->weeklySchedules->firstWhere('hari',$hari);
              $isWorkingDay = $sch && $sch->is_active;
              $dateStr = $cur->format('Y-m-d');
              
              if($isWorkingDay) {
                if(isset($absensiByDate[$dateStr])) {
                  $abs = $absensiByDate[$dateStr];
                  $statusMasuk = $abs->status_masuk;
                  
                  // Cek apakah hadir tapi terlambat
                  if ($statusMasuk === 'hadir' && $abs->keterlambatan) {
                    $terlambat++;
                  } elseif ($statusMasuk === 'hadir') {
                    $hadir++;
                  } elseif ($statusMasuk === 'terlambat') {
                    $terlambat++;
                  } elseif ($statusMasuk === 'izin') {
                    $izin++;
                  } elseif ($statusMasuk === 'sakit') {
                    $sakit++;
                  } elseif ($statusMasuk === 'alpa') {
                    $alpa++;
                  }
                } else {
                  // Hari kerja tapi tidak ada record = ALPA
                  $alpa++;
                }
              }
              $cur->addDay();
            }

            $totalKehadiran = $hadir + $terlambat;
            $percentage = $workDays > 0 ? round(($totalKehadiran / $workDays) * 100, 1) : 0;
            $status = $percentage >= 80 ? 'Baik' : ($percentage >= 60 ? 'Cukup' : 'Kurang');
            $statusClass = $percentage >= 80 ? 'status-good' : ($percentage >= 60 ? 'status-fair' : 'status-poor');
          @endphp
          <tr>
            <td class="tabular">{{ $index + 1 }}</td>
            <!-- Ganti uid dengan nip untuk menampilkan NUPTK pegawai, bukan UID RFID -->
            <td class="left tabular">{{ $guru->nip ?? '-' }}</td>
            <td class="left">{{ $guru->nama }}</td>
            <td>{{ $guru->jabatan ?? '-' }}</td>
            <td class="tabular">{{ $workDays }}</td>
            <td class="tabular">{{ $hadir }}</td>
            <td class="tabular">{{ $terlambat }}</td>
            <td class="tabular">{{ $izin }}</td>
            <td class="tabular">{{ $sakit }}</td>
            <td class="tabular">{{ $alpa }}</td>
            <td class="tabular">{{ $percentage }}%</td>
            <td class="{{ $statusClass }}">{{ $status }}</td>
          </tr>
        @empty
          <tr><td colspan="12" class="no-data">Tidak ada data guru untuk periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- TTD (1 baris, rata kanan) -->
  <div class="signature-wrap">
    <div class="sig">
      <div class="t">Mengetahui,</div>
      <div class="r">Kepala Sekolah</div>
      <div class="name">(...........................)</div>
    </div>
    <div class="sig">
      <div class="t">Dibuat Oleh,</div>
      <div class="r">Petugas Absensi</div>
      <div class="name">(...........................)</div>
    </div>
    <div class="sig">
      <div class="t">Disetujui,</div>
      <div class="r">Wakil Kepala Sekolah</div>
      <div class="name">(...........................)</div>
    </div>
  </div>

  <!-- FOOTER: legend kiri, info kanan -->
  <div class="footer">
    <div class="legend">
      <b>H</b>: Hadir &nbsp;•&nbsp; <b>T</b>: Terlambat &nbsp;•&nbsp; <b>I</b>: Izin &nbsp;•&nbsp; <b>S</b>: Sakit &nbsp;•&nbsp; <b>A</b>: Alpa
      &nbsp;&nbsp;|&nbsp;&nbsp; <b>Status</b>: Baik ≥80% • Cukup 60–79% • Kurang &lt;60%
    </div>
    <div>Sistem Presensi Guru Berbasis RFID</div>
  </div>
</div>
</body>
</html>
