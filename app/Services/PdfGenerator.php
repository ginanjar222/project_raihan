<?php

namespace App\Services;

use Carbon\Carbon;
use PDF;

class PdfGenerator
{
    /**
     * Generate PDF untuk rekap absensi mingguan dengan layout landscape
     * Menyederhanakan struktur data dan menggunakan template landscape baru
     */
    public function generateMingguanPdf($data, $start, $end, $fileName, $summary = null)
    {
        // Jika summary tidak diberikan dari controller, hitung sendiri
        if (is_null($summary)) {
            $summary = $this->calculateWeeklySummary($data, $start, $end);
        }
        
        $html = view('rekap.exports.weekly', [
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'week' => $start->week,
            'year' => $start->year,
            'summary' => $summary
        ])->render();

        return $this->generatePdfResponse($html, $fileName, 'A4', 'landscape');
    }

    /**
     * Generate PDF untuk rekap absensi bulanan
     */
    public function generateBulanPdf($data, $start, $end, $fileName, $summary = null)
    {
        // Jika summary tidak diberikan dari controller, hitung sendiri
        if (is_null($summary)) {
            $summary = $this->calculateMonthlySummary($data, $start, $end);
        }
        
        $html = view('rekap.exports.monthly', [
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'summary' => $summary
        ])->render();

        return $this->generatePdfResponse($html, $fileName, 'A4');
    }

    /**
     * Generate PDF untuk custom date range
     */
    public function generateCustomPdf($data, $start, $end, $fileName)
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

        $totals = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        $guruData = [];
        $totalWorkDays = 0;

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

            $guruData[] = [
                'guru' => $guru,
                'work_days' => $workDays,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total_kehadiran' => $totalKehadiran,
                'percentage' => $percentage,
                'status' => $status
            ];

            $totals['hadir'] += $hadir;
            $totals['terlambat'] += $terlambat;
            $totals['izin'] += $izin;
            $totals['sakit'] += $sakit;
            $totals['alpa'] += $alpa;
            $totalWorkDays += $workDays;
        }

        // Build HTML
        $html = view('rekap.export.pdf-custom-professional', [
            'data' => $guruData,
            'totals' => $totals,
            'totalWorkDays' => $totalWorkDays,
            'start' => $start,
            'end' => $end
        ])->render();

        return $this->generatePdfResponse($html, $fileName, 'portrait');
    }

    /**
     * Generate comprehensive professional PDF report with daily details, weekly recap, and monthly summary
     */
    public function generateComprehensiveReport($data, $start, $end, $schoolInfo = [], $fileName = 'Laporan-Absensi-Komprehensif')
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

        // Prepare daily attendance matrix
        $dailyMatrix = $this->buildDailyMatrix($data, $start, $end, $dayMapping);
        
        // Prepare weekly summary
        $weeklySummary = $this->buildWeeklySummary($data, $start, $end, $dayMapping);
        
        // Prepare monthly summary
        $monthlySummary = $this->buildMonthlySummary($data, $start, $end, $dayMapping);
        
        // Count teacher statistics
        $teacherStats = $this->countTeacherStats($data);

        $html = view('rekap.export.pdf-komprehensif-professional', [
            'data' => $data,
            'dailyMatrix' => $dailyMatrix,
            'weeklySummary' => $weeklySummary,
            'monthlySummary' => $monthlySummary,
            'teacherStats' => $teacherStats,
            'schoolInfo' => $schoolInfo,
            'start' => $start,
            'end' => $end
        ])->render();

        return $this->generatePdfResponse($html, $fileName, 'landscape');
    }

    /**
     * Generate PDF untuk laporan mingguan profesional
     * Revisi lengkap: mengirim data detail harian untuk setiap hari dalam minggu
     */
    public function generateMingguanPdfWeekly($data, $start, $end, $fileName)
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

        $totals = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        $guruData = [];

        foreach ($data as $guru) {
            $workDays = 0;
            $hadir = 0;
            $terlambat = 0;
            $izin = 0;
            $sakit = 0;
            $alpa = 0;
            
            // Array untuk menyimpan detail status setiap hari
            $dailyStatuses = [];
            $current = $start->copy();

            // Iterasi setiap hari dalam minggu
            while ($current <= $end) {
                $dayOfWeek = $current->dayOfWeek == 0 ? 7 : $current->dayOfWeek;
                $hariIndonesia = $dayMapping[$dayOfWeek];
                $schedule = $guru->weeklySchedules->firstWhere('hari', $hariIndonesia);
                $isWorkingDay = $schedule && $schedule->is_active;
                
                $absensi = $guru->absensi->firstWhere('tanggal', $current->toDateString());
                
                $dayStatus = [
                    'date' => $current->format('Y-m-d'),
                    'dayOfWeek' => $dayOfWeek,
                    'isWorkingDay' => $isWorkingDay,
                    'status' => null,
                    'display' => '-'
                ];

                // 1. Jika hari libur, tampilkan 'L'
                // 2. Jika ada absensi dengan jam_masuk, cek apakah terlambat atau hadir
                // 3. Jika ada absensi tapi tidak ada jam_masuk, cek izin/sakit/alpa
                // 4. Jika hari kerja tapi tidak ada record, berarti ALPA
                if (!$isWorkingDay) {
                    $dayStatus['status'] = 'libur';
                    $dayStatus['display'] = 'L';
                } elseif ($absensi && $absensi->jam_masuk) {
                    // Ada jam masuk
                    if ($absensi->status_masuk === 'terlambat') {
                        $dayStatus['status'] = 'terlambat';
                        $dayStatus['display'] = 'TL';
                        $terlambat++;
                    } else {
                        $dayStatus['status'] = 'hadir';
                        $dayStatus['display'] = 'H';
                        $hadir++;
                    }
                    $workDays++;
                } elseif ($absensi && $absensi->status_masuk === 'izin') {
                    $dayStatus['status'] = 'izin';
                    $dayStatus['display'] = 'I';
                    $izin++;
                    $workDays++;
                } elseif ($absensi && $absensi->status_masuk === 'sakit') {
                    $dayStatus['status'] = 'sakit';
                    $dayStatus['display'] = 'S';
                    $sakit++;
                    $workDays++;
                } elseif ($absensi && $absensi->status_masuk === 'alpa') {
                    $dayStatus['status'] = 'alpa';
                    $dayStatus['display'] = 'A';
                    $alpa++;
                    $workDays++;
                } elseif ($isWorkingDay && !$absensi) {
                    // Hari kerja tapi tidak ada record = ALPA
                    $dayStatus['status'] = 'alpa';
                    $dayStatus['display'] = 'A';
                    $alpa++;
                    $workDays++;
                }

                $dailyStatuses[] = $dayStatus;
                $current->addDay();
            }

            $totalKehadiran = $hadir + $terlambat;
            $percentage = $workDays > 0 ? round(($totalKehadiran / $workDays) * 100, 1) : 0;

            $guruData[] = [
                'guru' => $guru,
                'work_days' => $workDays,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total_kehadiran' => $totalKehadiran,
                'percentage' => $percentage,
                'dailyStatuses' => $dailyStatuses  // Tambah data detail harian
            ];

            $totals['hadir'] += $hadir;
            $totals['terlambat'] += $terlambat;
            $totals['izin'] += $izin;
            $totals['sakit'] += $sakit;
            $totals['alpa'] += $alpa;
        }

        $html = view('rekap.export.pdf-mingguan-weekly-professional', [
            'guruData' => $guruData,
            'totals' => $totals,
            'start' => $start,
            'end' => $end
        ])->render();

        return $this->generatePdfResponse($html, $fileName, 'portrait');
    }

    /**
     * Build daily attendance matrix for each teacher by date
     */
    private function buildDailyMatrix($data, $start, $end, $dayMapping)
    {
        $matrix = [];
        $current = $start->copy();
        $daysInRange = [];

        // Get all dates in range
        while ($current <= $end) {
            $daysInRange[] = $current->copy();
            $current->addDay();
        }

        // Build matrix for each teacher
        foreach ($data as $guru) {
            $guruDaily = [];
            $statusCounts = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

            foreach ($daysInRange as $date) {
                $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek;
                $hariIndonesia = $dayMapping[$dayOfWeek];
                $schedule = $guru->weeklySchedules->firstWhere('hari', $hariIndonesia);
                
                // Check if it's a working day
                $isWorkingDay = $schedule && $schedule->is_active;
                
                // Get attendance record for this date
                $absensi = $guru->absensi->firstWhere('tanggal', $date->toDateString());
                
                // Determine cell display
                $cellData = [
                    'date' => $date->copy(),
                    'dayOfWeek' => $dayOfWeek,
                    'isWorkingDay' => $isWorkingDay,
                    'isHoliday' => !$isWorkingDay,
                    'status' => null,
                    'absensi' => $absensi
                ];

                if ($absensi) {
                    if ($absensi->jam_masuk) {
                        $cellData['status'] = 'H';
                        $statusCounts['H']++;
                    } else if ($absensi->status_masuk === 'sakit') {
                        $cellData['status'] = 'S';
                        $statusCounts['S']++;
                    } else if ($absensi->status_masuk === 'izin') {
                        $cellData['status'] = 'I';
                        $statusCounts['I']++;
                    } else if ($absensi->status_masuk === 'alpa') {
                        $cellData['status'] = 'A';
                        $statusCounts['A']++;
                    }
                } elseif ($isWorkingDay) {
                    $cellData['status'] = 'A'; // Default to Alpa if no record on working day
                    $statusCounts['A']++;
                }

                $guruDaily[] = $cellData;
            }

            $matrix[] = [
                'guru' => $guru,
                'daily' => $guruDaily,
                'statusCounts' => $statusCounts
            ];
        }

        return $matrix;
    }

    /**
     * Build weekly summary from daily attendance data
     */
    private function buildWeeklySummary($data, $start, $end, $dayMapping)
    {
        $weeks = [];
        $current = $start->copy()->startOfWeek();
        $weekNumber = 1;

        while ($current <= $end) {
            $weekEnd = $current->copy()->endOfWeek();
            $weekEnd = min($weekEnd, $end);
            
            $weekData = [
                'week' => $weekNumber,
                'start' => $current->copy(),
                'end' => $weekEnd,
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0
            ];

            // Count attendance for this week
            foreach ($data as $guru) {
                $weekAbsensi = $guru->absensi->whereBetween('tanggal', [$current->toDateString(), $weekEnd->toDateString()]);
                
                foreach ($weekAbsensi as $absensi) {
                    if ($absensi->jam_masuk) {
                        $weekData['hadir']++;
                    } else if ($absensi->status_masuk === 'sakit') {
                        $weekData['sakit']++;
                    } else if ($absensi->status_masuk === 'izin') {
                        $weekData['izin']++;
                    } else if ($absensi->status_masuk === 'alpa') {
                        $weekData['alpa']++;
                    }
                }
            }

            $weeks[] = $weekData;
            $current->addWeek();
            $weekNumber++;
        }

        return $weeks;
    }

    /**
     * Build monthly summary for each teacher
     */
    private function buildMonthlySummary($data, $start, $end, $dayMapping)
    {
        $summary = [];

        foreach ($data as $guru) {
            $hadir = 0;
            $terlambat = 0;
            $izin = 0;
            $sakit = 0;
            $alpa = 0;
            $workDays = 0;

            // Count work days
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

            foreach ($guru->absensi as $absensi) {
                if ($absensi->jam_masuk) {
                    if ($absensi->status_masuk === 'terlambat') {
                        $terlambat++;
                    } else {
                        $hadir++;
                    }
                } else if ($absensi->status_masuk === 'sakit') {
                    $sakit++;
                } else if ($absensi->status_masuk === 'izin') {
                    $izin++;
                } else if ($absensi->status_masuk === 'alpa') {
                    $alpa++;
                }
            }

            $totalKehadiran = $hadir + $terlambat;
            $percentage = $workDays > 0 ? round(($totalKehadiran / $workDays) * 100, 1) : 0;
            $status = $percentage >= 80 ? 'Baik' : ($percentage >= 60 ? 'Cukup' : 'Kurang');

            $summary[] = [
                'guru' => $guru,
                'workDays' => $workDays,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'totalKehadiran' => $totalKehadiran,
                'percentage' => $percentage,
                'status' => $status
            ];
        }

        return $summary;
    }

    /**
     * Count teacher statistics by gender
     */
    private function countTeacherStats($data)
    {
        $total = count($data);
        $male = 0;
        $female = 0;

        // This assumes there's a gender field or similar. Adjust based on your schema
        // For now, we'll return basic counts
        return [
            'total' => $total,
            'male' => $male,
            'female' => $female
        ];
    }

    /**
     * Calculate summary statistics for weekly report
     */
    private function calculateWeeklySummary($data, $start, $end)
    {
        $summary = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'libur' => 0
        ];

        $dayMap = [1=>'senin', 2=>'selasa', 3=>'rabu', 4=>'kamis', 5=>'jumat', 6=>'sabtu', 7=>'minggu'];

        foreach ($data as $guru) {
            $absensiByDate = [];
            foreach ($guru->absensi as $abs) {
                $absensiByDate[$abs->tanggal->format('Y-m-d')] = $abs;
            }

            $current = $start->copy();
            while ($current <= $end) {
                $dow = $current->dayOfWeek == 0 ? 7 : $current->dayOfWeek;
                $hari = $dayMap[$dow];
                $schedule = $guru->weeklySchedules->firstWhere('hari', $hari);
                $isWorkingDay = $schedule && $schedule->is_active;
                $dateStr = $current->format('Y-m-d');

                if ($isWorkingDay) {
                    if (isset($absensiByDate[$dateStr])) {
                        $abs = $absensiByDate[$dateStr];
                        $statusMasuk = $abs->status_masuk;

                        if ($statusMasuk === 'hadir' && $abs->keterlambatan) {
                            $summary['terlambat']++;
                        } elseif ($statusMasuk === 'hadir') {
                            $summary['hadir']++;
                        } elseif ($statusMasuk === 'terlambat') {
                            $summary['terlambat']++;
                        } elseif ($statusMasuk === 'izin') {
                            $summary['izin']++;
                        } elseif ($statusMasuk === 'sakit') {
                            $summary['sakit']++;
                        } elseif ($statusMasuk === 'alpa') {
                            $summary['alpa']++;
                        }
                    } else {
                        $summary['alpa']++;
                    }
                } else {
                    $summary['libur']++;
                }

                $current->addDay();
            }
        }

        return $summary;
    }

    /**
     * Calculate summary statistics for monthly report
     */
    private function calculateMonthlySummary($data, $start, $end)
    {
        $summary = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0
        ];

        $dayMap = [1=>'senin', 2=>'selasa', 3=>'rabu', 4=>'kamis', 5=>'jumat', 6=>'sabtu', 7=>'minggu'];

        foreach ($data as $guru) {
            $absensiByDate = [];
            foreach ($guru->absensi as $abs) {
                $absensiByDate[$abs->tanggal->format('Y-m-d')] = $abs;
            }

            $current = $start->copy();
            while ($current <= $end) {
                $dow = $current->dayOfWeek == 0 ? 7 : $current->dayOfWeek;
                $hari = $dayMap[$dow];
                $schedule = $guru->weeklySchedules->firstWhere('hari', $hari);
                $isWorkingDay = $schedule && $schedule->is_active;
                $dateStr = $current->format('Y-m-d');

                if ($isWorkingDay) {
                    if (isset($absensiByDate[$dateStr])) {
                        $abs = $absensiByDate[$dateStr];
                        $statusMasuk = $abs->status_masuk;

                        if ($statusMasuk === 'hadir' && $abs->keterlambatan) {
                            $summary['terlambat']++;
                        } elseif ($statusMasuk === 'hadir') {
                            $summary['hadir']++;
                        } elseif ($statusMasuk === 'terlambat') {
                            $summary['terlambat']++;
                        } elseif ($statusMasuk === 'izin') {
                            $summary['izin']++;
                        } elseif ($statusMasuk === 'sakit') {
                            $summary['sakit']++;
                        } elseif ($statusMasuk === 'alpa') {
                            $summary['alpa']++;
                        }
                    } else {
                        $summary['alpa']++;
                    }
                }

                $current->addDay();
            }
        }

        return $summary;
    }

    /**
     * Generate PDF response using Laravel PDF Facade
     */
    private function generatePdfResponse($html, $fileName, $size = 'A4', $orientation = 'portrait')
    {
        return PDF::loadHTML($html)
            ->setPaper($size, $orientation)
            ->download($fileName . '.pdf');
    }
}
