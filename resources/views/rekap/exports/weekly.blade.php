<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Laporan Presensi Guru Mingguan</title>
<style>
  /* ===== BASE ===== */
  *{margin:0;padding:0;box-sizing:border-box}
  html,body{background:#fff;color:#111;font:9px/1.35 "Tahoma","Segoe UI",Arial,sans-serif;-webkit-print-color-adjust:exact;print-color-adjust:exact}
  .tabular{font-variant-numeric:tabular-nums}
  @page{ size:A4 landscape; margin:8mm 8mm 9mm 8mm; }
  @media print{ thead{display:table-header-group} tfoot{display:table-footer-group} }

  .sheet{border:1px solid #000;border-radius:2px;padding:6mm;margin:1mm 0}

  /* ===== HEADER ===== */
  .title{font-weight:800;font-size:11.5px;text-align:center}
  .sub{font-size:8.5px;text-align:center;margin-top:1mm}
  .meta-inline{
    display:flex;justify-content:space-between;align-items:flex-start;
    margin:3.5mm 0 2.5mm;font-size:8px
  }
  .meta-left{white-space:nowrap}
  .meta-right{text-align:right}
  .divider{border-top:1px solid #000;margin:3mm 0}

  /* ===== SUMMARY (horizontal) ===== */
  .summary-bar{margin:2mm 0 3mm}
  .summary-table{width:100%;border-collapse:collapse}
  .summary-table th,.summary-table td{border:1px solid #000;padding:1.8mm 2mm;text-align:center}
  .summary-table th{background:#efefef;font-weight:800}
  .summary-table td{font-weight:700}

  /* ===== TABLE CARD ===== */
  .table-card{border:1px solid #000;border-radius:2px;padding:3.5mm;margin:2mm 0 3mm}
  .table-caption{text-align:center;font-weight:800;margin-bottom:2mm;font-size:9px}

  /* ===== MAIN TABLE ===== */
  table{width:100%;border-collapse:collapse}
  th,td{border:1px solid #000;padding:2.3mm 1.5mm;text-align:center;vertical-align:middle}
  .left{text-align:left}
  tbody tr:nth-child(odd){background:#fafafa}
  tr{page-break-inside:avoid}

  th[col-no]{width:3%}
  th[col-nuptk]{width:9%}
  th[col-nama]{width:16%}
  th[col-bidang]{width:11%}
  th[col-day]{width:2.5%}
  th[col-sum]{width:3%}
  thead{background:#1a5490;color:#fff}
  thead th{border-color:#16477d;font-weight:800}
  /* Tambahkan warna styling untuk cell status seperti bulanan */
  thead .upper{background:#1a5490;font-weight:800}
  thead .lower{background:#2d7cb5;font-weight:700}
  .is-weekend{background:#e8f4f8!important}
  .wknd-cell{background:#f1f1f1}
  .badge{font-weight:900;font-size:10px}
  /* Warna untuk status yang berbeda */
  .status-hadir{color:#27ae60;font-weight:900}
  .status-terlambat{color:#f39c12;font-weight:900}
  .status-izin{color:#3498db;font-weight:900}
  .status-sakit{color:#9b59b6;font-weight:900}
  .status-alpa{color:#e74c3c;font-weight:900}
  .status-libur{color:#95a5a6;font-weight:900}

  /* ===== SIGNATURE: 1 baris, rata kanan, tanpa garis pemisah antar blok ===== */
  .signature-wrap{
    margin-top:4mm; display:flex; justify-content:flex-end; gap:8mm;
    font-size:7.5px; align-items:flex-end;
  }
  .sig{ text-align:center; min-width:45mm }
  .sig .t{ font-weight:700; margin-bottom:.6mm }
  .sig .r{ font-size:7px; color:#333; margin-bottom:6mm }
  .sig .name{ font-size:7px } /* tanpa garis panjang */

  /* ===== FOOTER (bersih; kiri = keterangan, kanan = info) ===== */
  .footer{
    margin-top:3mm; display:flex; justify-content:space-between; align-items:flex-end;
    font-size:7.6px; color:#333;
  }
  .legend{font-size:7.6px}
  .legend b{font-weight:800}
  .footer-right{text-align:right}
  .no-data{text-align:center;padding:6mm 0;color:#555;font-style:italic}
</style>
</head>
<body>
@php
  $days=[]; $c=$start->copy(); while($c <= $end){ $days[]=$c->copy(); $c->addDay(); }
@endphp

<div class="sheet">
  <!-- HEADER -->
  <div class="title">Laporan Presensi Guru Mingguan</div>
  <div class="sub">SMA YKBBB LELES</div>

  <!-- Bulan/Tahun sebagai teks biasa (bukan tabel) -->
  <div class="meta-inline">
    <div class="meta-left">
      <strong>Bulan:</strong> <span class="tabular">{{ $start->format('m') }}</span>
      &nbsp;&nbsp;|&nbsp;&nbsp;
      <strong>Tahun:</strong> <span class="tabular">{{ $start->format('Y') }}</span>
    </div>
    <div class="meta-right">
      Periode: <strong>{{ $start->format('d F Y') }} – {{ $end->format('d F Y') }}</strong><br>
      Minggu Ke-{{ $week }} • Tahun Ajaran {{ $year }}/{{ $year+1 }}<br>
      Cetak: {{ now()->format('d/m/Y H:i') }}
    </div>
  </div>

  <div class="divider"></div>

  <!-- SUMMARY (horizontal) -->
  <div class="summary-bar">
    <table class="summary-table">
      <tr>
        <th>Total Guru</th><td>{{ $data->count() }}</td>
        <th>Hadir</th><td>{{ $summary['hadir'] ?? 0 }}</td>
        <th>Terlambat</th><td>{{ $summary['terlambat'] ?? 0 }}</td>
        <th>Izin</th><td>{{ $summary['izin'] ?? 0 }}</td>
        <th>Sakit</th><td>{{ $summary['sakit'] ?? 0 }}</td>
        <th>Alpa</th><td>{{ $summary['alpa'] ?? 0 }}</td>
        <th>Libur</th><td>{{ $summary['libur'] ?? 0 }}</td>
      </tr>
    </table>
  </div>

  <!-- TABEL UTAMA -->
  <div class="table-card">
    <div class="table-caption">Rekapitulasi Presensi Mingguan</div>
    <!-- Tambah debug untuk melihat data yang diterima -->
    @if(env('APP_DEBUG'))
      <div style="font-size: 7px; color: #999; margin-bottom: 2mm; padding: 1mm; background: #f0f0f0;">
        DEBUG: Total Guru: {{ count($data) }} | Guru pertama absensi: {{ count($data->first()?->absensi ?? []) }}
      </div>
    @endif
    <table>
      <thead>
        <tr class="upper">
          <th col-no rowspan="2">NO</th>
          <th col-nuptk rowspan="2">NUPTK</th>
          <th col-nama rowspan="2">NAMA</th>
          <th col-bidang rowspan="2">BIDANG STUDI</th>

          @foreach($days as $d)
            @php $wk=in_array($d->format('N'),[6,7])?'is-weekend':''; @endphp
            <th col-day class="{{ $wk }}" rowspan="2">
              {{ $d->format('d') }}<br><small>{{ strtoupper($d->locale('id')->translatedFormat('D')) }}</small>
            </th>
          @endforeach

          <th colspan="6">JUMLAH</th>
        </tr>
        <tr class="lower">
          <th col-sum>H</th><th col-sum>T</th><th col-sum>I</th><th col-sum>S</th><th col-sum>A</th><th col-sum>L</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $i=>$guru)
          @php $s=['hadir'=>0,'terlambat'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0, 'libur'=>0]; @endphp
          <tr>
            <td class="tabular">{{ $i+1 }}</td>
            <!-- Ganti uid dengan nip untuk menampilkan NUPTK pegawai, bukan UID RFID -->
            <td class="left tabular">{{ $guru->nip ?? '-' }}</td>
            <td class="left">{{ $guru->nama }}</td>
            <td>{{ $guru->jabatan ?? '-' }}</td>
            @foreach($days as $d)
              @php
                $dateKey = $d->format('Y-m-d');
                $abs = $guru->absensi->firstWhere(function($item) use ($dateKey) {
                    return $item->tanggal->format('Y-m-d') === $dateKey;
                });
                
                $dayOfWeek = $d->dayOfWeek == 0 ? 7 : $d->dayOfWeek;
                $dayMapping = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'];
                $hariIndonesia = $dayMapping[$dayOfWeek];
                $schedule = $guru->weeklySchedules->where('hari', $hariIndonesia)->first();
                $isWorkingDay = $schedule && $schedule->is_active;
                
                if (!$isWorkingDay) {
                    // Hari libur
                    $st = 'libur';
                    $display = 'L';
                    $statusClass = 'status-libur';
                } elseif ($abs) {
                    // Ada record absensi - cek status_masuk dan keterlambatan
                    $statusMasuk = $abs->status_masuk;
                    
                    if ($statusMasuk === 'hadir' && $abs->keterlambatan) {
                        // Hadir tapi terlambat = Terlambat
                        $st = 'terlambat';
                        $display = 'T';
                        $statusClass = 'status-terlambat';
                    } elseif ($statusMasuk === 'izin' || $statusMasuk === 'sakit') {
                        // Input manual izin/sakit - gunakan status dari record
                        $st = $statusMasuk;
                        $display = strtoupper(substr($statusMasuk, 0, 1));
                        $statusClass = 'status-' . $statusMasuk;
                    } else {
                        // Status lainnya (hadir, terlambat, alpa)
                        $st = $statusMasuk;
                        $display = strtoupper(substr($statusMasuk, 0, 1));
                        $statusClass = 'status-' . $statusMasuk;
                    }
                } else {
                    // Hari kerja tapi tidak ada record = ALPA
                    $st = 'alpa';
                    $display = 'A';
                    $statusClass = 'status-alpa';
                }
                
                // Update counter - hanya untuk hari kerja
                if ($st !== 'libur') {
                    $s[$st]++;
                }
                $wkcell=in_array($d->format('N'),[6,7]) ? 'wknd-cell' : '';
              @endphp
              <td class="{{ $wkcell }}"><span class="badge {{ $statusClass }}">{{ $display }}</span></td>
            @endforeach
            <td class="tabular">{{ $s['hadir'] }}</td>
            <td class="tabular">{{ $s['terlambat'] }}</td>
            <td class="tabular">{{ $s['izin'] }}</td>
            <td class="tabular">{{ $s['sakit'] }}</td>
            <td class="tabular">{{ $s['alpa'] }}</td>
            <td class="tabular">{{ $s['libur'] }}</td>
          </tr>
        @empty
          <tr><td colspan="{{ 4 + count($days) + 6 }}" class="no-data">Tidak ada data guru untuk periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- TTD: 1 baris, rata kanan -->
  <div class="signature-wrap">
    <div class="sig">
      <div class="t">Mengetahui,</div>
      <div class="r">Kepala Sekolah</div>
      <div class="name">(...........................)</div>
    </div>
    <div class="sig">
      <div class="t">Dibuat Oleh,</div>
      <div class="r">Petugas Presensi</div>
      <div class="name">(...........................)</div>
    </div>
    <div class="sig">
      <div class="t">Disetujui,</div>
      <div class="r">Wakil Kepala Sekolah</div>
      <div class="name">(...........................)</div>
    </div>
  </div>

  <!-- FOOTER: kiri = Keterangan (teks), kanan = info sistem (tanpa garis) -->
  <div class="footer">
    <div class="legend">
      <b>H</b>: Hadir &nbsp;•&nbsp; <b>T</b>: Terlambat &nbsp;•&nbsp; <b>I</b>: Izin &nbsp;•&nbsp; <b>S</b>: Sakit &nbsp;•&nbsp; <b>A</b>: Alpa &nbsp;•&nbsp; <b>L</b>: Libur
    </div>
    <div class="footer-right">Sistem Presensi Guru Berbasis RFID</div>
  </div>
</div>
</body>
</html>
