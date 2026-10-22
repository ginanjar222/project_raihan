<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiService
{
    public function validateScheduleAndDetermineStatus($guru, $currentTime)
    {
        try {
            // Set timezone konsisten
            Carbon::setLocale('id');
            $now = Carbon::parse($currentTime)->setTimezone('Asia/Jakarta');
            
            // Jika tidak ada jadwal jam masuk, langsung HADIR
            if (!$guru->jam_masuk) {
                return [
                    'isValid' => true,
                    'status' => 'HADIR',
                    'keterlambatan' => null,
                    'message' => 'Absensi masuk berhasil!'
                ];
            }
            
            // Parse jam masuk dari guru (format H:i:s atau H:i)
            $jamMasukStr = $guru->jam_masuk;
            if (strlen($jamMasukStr) == 5) { // Format H:i
                $jamMasukStr .= ':00'; // Tambahkan detik
            }
            
            $jamMasuk = Carbon::createFromFormat('H:i:s', $jamMasukStr, 'Asia/Jakarta');
            $jamMasuk->setDate($now->year, $now->month, $now->day);
            
            // Toleransi 30 menit sebelum jam masuk (bisa absen lebih awal)
            $batasAwal = $jamMasuk->copy()->subMinutes(30);
            
            // Toleransi 30 menit setelah jam masuk (masih dianggap tepat waktu)
            $batasTerlambat = $jamMasuk->copy()->addMinutes(30);
            
            \Log::info("Validasi Jadwal Masuk:", [
                'guru' => $guru->nama,
                'jam_masuk_jadwal' => $jamMasuk->format('H:i:s'),
                'waktu_sekarang' => $now->format('H:i:s'),
                'batas_awal' => $batasAwal->format('H:i:s'),
                'batas_terlambat' => $batasTerlambat->format('H:i:s')
            ]);
            
            // Cek apakah terlalu awal
            if ($now->lt($batasAwal)) {
                return [
                    'isValid' => false,
                    'message' => "Belum waktunya absen. Silakan coba lagi setelah jam {$batasAwal->format('H:i')}"
                ];
            }
            
            // Cek apakah masih dalam toleransi (HADIR)
            if ($now->lte($batasTerlambat)) {
                return [
                    'isValid' => true,
                    'status' => 'HADIR',
                    'keterlambatan' => null,
                    'message' => 'Absensi masuk berhasil!'
                ];
            }
            
            // Jika lebih dari toleransi = TERLAMBAT
            $selisihMenit = $now->diffInMinutes($jamMasuk);
            $jam = intval($selisihMenit / 60);
            $menit = $selisihMenit % 60;
            $keterlambatan = sprintf('%02d:%02d', $jam, $menit);
            
            return [
                'isValid' => true,
                'status' => 'TERLAMBAT',
                'keterlambatan' => $keterlambatan,
                'message' => "Absensi masuk berhasil! Anda terlambat {$keterlambatan}"
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error validating schedule: ' . $e->getMessage());
            return [
                'isValid' => true,
                'status' => 'HADIR',
                'keterlambatan' => null,
                'message' => 'Absensi masuk berhasil!'
            ];
        }
    }
    
    public function validatePulangTime($guru, $currentTime, $existingAbsen)
    {
        try {
            // Set timezone konsisten
            $now = Carbon::parse($currentTime)->setTimezone('Asia/Jakarta');
            
            // Jika tidak ada jadwal jam pulang, tidak bisa pulang
            if (!$guru->jam_pulang) {
                return [
                    'isValid' => false,
                    'message' => 'Jadwal jam pulang tidak ditemukan. Hubungi administrator.'
                ];
            }
            
            // Parse jam pulang dari guru
            $jamPulangStr = $guru->jam_pulang;
            if (strlen($jamPulangStr) == 5) { // Format H:i
                $jamPulangStr .= ':00'; // Tambahkan detik
            }
            
            $jamPulang = Carbon::createFromFormat('H:i:s', $jamPulangStr, 'Asia/Jakarta');
            $jamPulang->setDate($now->year, $now->month, $now->day);
            
            \Log::info("Validasi Waktu Pulang:", [
                'guru' => $guru->nama,
                'jam_pulang_jadwal' => $jamPulang->format('H:i:s'),
                'waktu_sekarang' => $now->format('H:i:s'),
                'bisa_pulang' => $now->gte($jamPulang)
            ]);
            
            // Cek apakah sudah waktunya pulang
            if ($now->lt($jamPulang)) {
                $sisaWaktu = $jamPulang->diff($now);
                $sisaJam = $sisaWaktu->h;
                $sisaMenit = $sisaWaktu->i;
                
                return [
                    'isValid' => false,
                    'message' => "Belum waktunya pulang! Jadwal pulang Anda jam {$jamPulang->format('H:i')}. Sisa waktu: {$sisaJam} jam {$sisaMenit} menit"
                ];
            }
            
            return [
                'isValid' => true,
                'message' => 'Waktu pulang valid'
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error validating pulang time: ' . $e->getMessage());
            return [
                'isValid' => false,
                'message' => 'Terjadi kesalahan validasi waktu pulang. Silakan hubungi administrator.'
            ];
        }
    }
    
    public function getDashboardStats()
    {
        try {
            $today = Carbon::today('Asia/Jakarta');
            
            \Log::info('Dashboard Stats Query - Today: ' . $today->format('Y-m-d'));
            
            $totalGuru = Guru::count();
            \Log::info('Total Guru: ' . $totalGuru);
            
            $sudahAbsen = Absensi::whereDate('tanggal', $today)
                                ->whereIn('status_masuk', ['hadir', 'terlambat'])
                                ->count();
            \Log::info('Sudah Absen (hadir+terlambat): ' . $sudahAbsen);
            
            $totalYangSudahAdaRecord = Absensi::whereDate('tanggal', $today)->count();
            \Log::info('Total Yang Sudah Ada Record: ' . $totalYangSudahAdaRecord);
            
            $belumAbsen = $totalGuru - $totalYangSudahAdaRecord;
            
            $hadir = Absensi::whereDate('tanggal', $today)->where('status_masuk', 'hadir')->count();
            $terlambat = Absensi::whereDate('tanggal', $today)->where('status_masuk', 'terlambat')->count();
            $izin = Absensi::whereDate('tanggal', $today)->where('status_masuk', 'izin')->count();
            $sakit = Absensi::whereDate('tanggal', $today)->where('status_masuk', 'sakit')->count();
            $alpa = Absensi::whereDate('tanggal', $today)->where('status_masuk', 'alpa')->count();
            
            \Log::info('Status Counts - Hadir: ' . $hadir . ', Terlambat: ' . $terlambat . ', Izin: ' . $izin . ', Sakit: ' . $sakit . ', Alpa: ' . $alpa);
            
            \Log::info('Database Connection: ' . config('database.default'));
            \Log::info('Database Name: ' . config('database.connections.' . config('database.default') . '.database'));
            
            // Fallback untuk data lama yang menggunakan keterangan_masuk
            $terlambatLama = Absensi::whereDate('tanggal', $today)
                                    ->where('keterangan_masuk', 'TERLAMBAT')
                                    ->whereNull('status_masuk')
                                    ->count();
            
            $totalHadirFisik = $hadir + $terlambat + $terlambatLama;
            $sudahPulang = Absensi::whereDate('tanggal', $today)
                                 ->whereIn('status_masuk', ['hadir', 'terlambat'])
                                 ->whereNotNull('jam_keluar')
                                 ->count();
            $masihDiSekolah = $totalHadirFisik - $sudahPulang;
            
            $result = [
                'totalGuru' => $totalGuru,
                'sudahAbsen' => $sudahAbsen, // Hanya hadir + terlambat
                'belumAbsen' => $belumAbsen, // Hanya yang benar-benar belum ada record absensi
                'hadir' => $hadir,
                'terlambat' => $terlambat + $terlambatLama,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'terlambatHariIni' => $terlambat + $terlambatLama,
                'sudahPulang' => $sudahPulang,
                'masihDiSekolah' => $masihDiSekolah,
                'totalTidakHadir' => $izin + $sakit + $alpa, // Total yang tidak hadir dengan keterangan
                'totalHadirFisik' => $totalHadirFisik // Total yang benar-benar hadir di sekolah
            ];
            
            \Log::info('Dashboard Stats Result: ', $result);
            
            return $result;
            
        } catch (\Exception $e) {
            \Log::error('Error in getDashboardStats: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return default values on error
            return [
                'totalGuru' => 0,
                'sudahAbsen' => 0,
                'belumAbsen' => 0,
                'hadir' => 0,
                'terlambat' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpa' => 0,
                'terlambatHariIni' => 0,
                'sudahPulang' => 0,
                'masihDiSekolah' => 0,
                'totalTidakHadir' => 0,
                'totalHadirFisik' => 0
            ];
        }
    }
}
