<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UIDController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [AbsensiController::class, 'scanPage'])->name('scan.page');
Route::get('/scan', [AbsensiController::class, 'scanPage'])->name('scan');

Route::post('/scan/process', [ScanController::class, 'process'])->name('scan.process');
Route::get('/scan/status', [AbsensiController::class, 'getScanStatus'])->name('scan.status');

// Routes untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

    // Guru Management
    Route::resource('guru', GuruController::class);
    Route::get('/guru/export', [GuruController::class, 'export'])->name('guru.export');
    
    Route::get('/guru/{id}/weekly-schedule', [GuruController::class, 'weeklySchedule'])->name('guru.weekly-schedule');
    Route::put('/guru/{id}/weekly-schedule', [GuruController::class, 'updateWeeklySchedule'])->name('guru.weekly-schedule.update');

    // Manual Absensi
    Route::get('/absensi/manual', [AbsensiController::class, 'manualForm'])->name('absensi.manual');
    Route::post('/absensi/manual', [AbsensiController::class, 'manualStore'])->name('absensi.manual.store');

    // List dan Edit Absensi
    Route::get('/absensi/list', [AbsensiController::class, 'listAbsensi'])->name('absensi.list');
    Route::get('/absensi/{id}/edit-keterangan', [AbsensiController::class, 'editKeterangan'])->name('absensi.edit.keterangan');
    Route::put('/absensi/{id}/keterangan', [AbsensiController::class, 'updateKeterangan'])->name('absensi.update.keterangan');
    Route::get('/absensi/{id}/photos', [ScanController::class, 'showPhotos'])->name('absensi.photos');
    Route::get('/absensi/{id}/evidence', [AbsensiController::class, 'viewEvidence'])->name('absensi.evidence');
    
    // Evidence Management
    Route::get('/absensi/evidence-list', [AbsensiController::class, 'evidenceList'])->name('absensi.evidence.list');
    Route::get('/absensi/{id}/download-evidence', [AbsensiController::class, 'downloadEvidence'])->name('absensi.evidence.download');
    Route::delete('/absensi/{id}/delete-evidence', [AbsensiController::class, 'deleteEvidence'])->name('absensi.evidence.delete');
    
    Route::get('/storage/absensi-photos/{filename}', [ScanController::class, 'servePhoto'])
        ->name('absensi.photo.serve')
        ->where('filename', '.*');

    // Rekap
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/mingguan', [RekapController::class, 'mingguan'])->name('rekap.mingguan');
    Route::get('/rekap/bulanan', [RekapController::class, 'bulanan'])->name('rekap.bulanan');
    Route::get('/rekap/mingguan/export', [RekapController::class, 'exportMingguan'])->name('rekap.mingguan.export');
    Route::get('/rekap/bulanan/export', [RekapController::class, 'exportBulanan'])->name('rekap.bulanan.export');
    
    // Test route untuk debugging
    Route::get('/test-scan/{uid}', [AbsensiController::class, 'testScan'])->name('test.scan');
    
    Route::post('/set-uid', [UIDController::class, 'setUID']);
    Route::get('/get-uid', [UIDController::class, 'getUID'])->name('uid.get');
});

Route::post('/rfid', function (Request $request) {
    $uid = $request->uid;
    \App\Models\Absensi::create([
        'uid' => $uid,
        'waktu' => now()
    ]);
    return response()->json(['status' => 'success', 'uid' => $uid]);
});

Route::get('/uid-latest', function () {
    $uid = file_get_contents(storage_path('app/uid.txt'));
    return response()->json(['uid' => $uid]);
});
