<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Absensi;
use App\Services\PdfExportService;
use App\Services\PdfGenerator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    protected $exportService;
    protected $pdfGenerator;

    public function __construct(PdfExportService $exportService, PdfGenerator $pdfGenerator)
    {
        $this->exportService = $exportService;
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * Hitung summary statistik untuk periode tertentu
     */
    private function calculateSummary($data, $start, $end)
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

                        // Jika hadir tapi terlambat, hitung sebagai terlambat
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
                        // Hari kerja tanpa record = ALPA
                        $summary['alpa']++;
                    }
                } else {
                    // Hari libur (bukan hari kerja menurut jadwal)
                    $summary['libur']++;
                }

                $current->addDay();
            }
        }

        return $summary;
    }

    /**
     * Display weekly attendance recap
     */
    public function mingguan(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $week = $request->get('week', Carbon::now()->week);
        
        $year = max(2020, min($year, Carbon::now()->year + 1));
        $week = max(1, min($week, 53));
        
        $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
        
        $prevWeek = $week > 1 ? $week - 1 : 53;
        $prevYear = $week > 1 ? $year : $year - 1;
        $nextWeek = $week < 53 ? $week + 1 : 1;
        $nextYear = $week < 53 ? $year : $year + 1;
        
        $canGoPrev = !($prevYear < 2020);
        $canGoNext = !($nextYear > Carbon::now()->year + 1);
        
        $data = Guru::with(['absensi' => function($query) use ($start, $end) {
            $query->whereBetween('tanggal', [$start, $end]);
        }, 'weeklySchedules'])->get();
        
        $summary = $this->calculateSummary($data, $start, $end);
        
        return view('rekap.mingguan', compact('data', 'start', 'end', 'year', 'week', 'prevWeek', 'prevYear', 'nextWeek', 'nextYear', 'canGoPrev', 'canGoNext', 'summary'));
    }

    /**
     * Display monthly attendance recap
     */
    public function bulanan(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        $year = max(2020, min($year, Carbon::now()->year + 1));
        $month = max(1, min($month, 12));
        
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $prevMonth = $month > 1 ? $month - 1 : 12;
        $prevYear = $month > 1 ? $year : $year - 1;
        $nextMonth = $month < 12 ? $month + 1 : 1;
        $nextYear = $month < 12 ? $year : $year + 1;
        
        $canGoPrev = !($prevYear < 2020);
        $canGoNext = !($nextYear > Carbon::now()->year + 1);
        
        $data = Guru::with(['absensi' => function($query) use ($start, $end) {
            $query->whereBetween('tanggal', [$start, $end]);
        }, 'weeklySchedules'])->get();
        
        $summary = $this->calculateSummary($data, $start, $end);
        
        return view('rekap.bulanan', compact('data', 'start', 'end', 'year', 'month', 'prevMonth', 'prevYear', 'nextMonth', 'nextYear', 'canGoPrev', 'canGoNext', 'summary'));
    }

    private function getWorkDaysForGuru($guru, $start, $end)
    {
        $workDays = 0;
        $current = $start->copy();
        
        $dayMapping = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            7 => 'minggu'
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
        
        return $workDays;
    }

    /**
     * Export weekly attendance report as PDF
     */
    public function exportMingguan(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $week = $request->get('week', Carbon::now()->week);
        
        $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
        
        $data = Guru::with([
            'absensi' => function($query) use ($start, $end) {
                // Filter absensi hanya yang dalam periode start-end
                $query->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                      ->orderBy('tanggal', 'asc');
            },
            'weeklySchedules' => function($query) {
                // Load jadwal mingguan aktif saja
                $query->where('is_active', true);
            }
        ])->get();
        
        $summary = $this->calculateSummary($data, $start, $end);

        try {
            $fileName = 'Laporan-Absensi-Guru-Mingguan-' . $start->format('d-m-Y') . '_' . $end->format('d-m-Y');
            return $this->pdfGenerator->generateMingguanPdf($data, $start, $end, $fileName, $summary);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Export monthly attendance report as PDF
     */
    public function exportBulanan(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $data = Guru::with([
            'absensi' => function($query) use ($start, $end) {
                // Filter absensi hanya yang dalam periode start-end
                $query->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                      ->orderBy('tanggal', 'asc');
            },
            'weeklySchedules' => function($query) {
                // Load jadwal mingguan aktif saja
                $query->where('is_active', true);
            }
        ])->get();
        
        $summary = $this->calculateSummary($data, $start, $end);

        try {
            $fileName = 'Laporan-Absensi-Guru-Bulanan-' . $start->format('F-Y');
            return $this->pdfGenerator->generateBulanPdf($data, $start, $end, $fileName, $summary);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan rekap dengan filter custom date range
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
        }

        $rekapData = $this->exportService->getRekapData($start->format('Y-m-d'), $end->format('Y-m-d'));

        return view('rekap.index', [
            'data' => $rekapData['data'],
            'start' => $rekapData['start'],
            'end' => $rekapData['end'],
            'summary' => $rekapData['summary'],
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d')
        ]);
    }
}
