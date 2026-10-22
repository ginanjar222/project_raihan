<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AbsensiService;

class DashboardController extends Controller
{
    protected $absensiService;
    
    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }
    
    public function index()
    {
        $stats = $this->absensiService->getDashboardStats();
        
        return view('dashboard', [
            'totalGuru' => $stats['totalGuru'],
            'sudahAbsen' => $stats['sudahAbsen'],
            'belumAbsen' => $stats['belumAbsen'],
            'terlambatHariIni' => $stats['terlambatHariIni'],
            'sudahPulang' => $stats['sudahPulang'],
            'masihDiSekolah' => $stats['masihDiSekolah']
        ]);
    }
    
    public function getStats()
    {
        return response()->json($this->absensiService->getDashboardStats());
    }
}
