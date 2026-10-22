<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Absensi;
use App\Models\AbsensiPhoto;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    protected $absensiService;
    
    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }
    
    public function index()
    {
        return view('scan');
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'face_descriptor' => 'nullable|string'
        ]);
        
        $uid = $request->uid;
        $guru = Guru::with('weeklySchedules')->where('uid', $uid)->first();
        $verification = $this->verifyFace($guru, $request->face_descriptor);

        \Log::info("Scan Process:", [
            'uid' => $uid,
            'guru_found' => $guru ? $guru->nama : 'tidak ditemukan',
            'has_photo' => $request->hasFile('photo'),
            'face_verification' => $verification['status']
        ]);

        $photoPath = null;
        $photoCapturedAt = Carbon::now('Asia/Jakarta');

        // ==== Simpan Foto ====
        if ($request->hasFile('photo')) {
            try {
                $photo = $request->file('photo');
                $fileName = 'capture_' . $uid . '_' . $photoCapturedAt->format('Y-m-d_H-i-s') . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('absensi_photos', $fileName, 'public');

                \Log::info("Photo captured and saved:", [
                    'uid' => $uid,
                    'photo_path' => $photoPath,
                    'file_size' => $photo->getSize()
                ]);

            } catch (\Exception $e) {
                \Log::error("Error saving photo: " . $e->getMessage());
            }
        }

        // ==== Jika kartu tidak dikenal ====
        if (!$guru) {
            if ($photoPath) {
                AbsensiPhoto::create([
                    'guru_id' => null, // <== FIX di sini (null bukan 0)
                    'absensi_id' => null,
                    'uid' => $uid,
                    'photo_path' => $photoPath,
                    'capture_type' => 'failed_attempt',
                    'capture_context' => 'Kartu tidak terdaftar dalam sistem',
                    'captured_at' => $photoCapturedAt
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak terdaftar dalam sistem'
            ]);
        }
        
        $today = Carbon::today('Asia/Jakarta');
        $currentTime = Carbon::now('Asia/Jakarta');
        
        $dayName = strtolower($currentTime->locale('id')->dayName);
        $dayMapping = [
            'senin' => 'senin',
            'selasa' => 'selasa', 
            'rabu' => 'rabu',
            'kamis' => 'kamis',
            'jumat' => 'jumat',
            'sabtu' => 'sabtu',
            'minggu' => 'minggu',
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu', 
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
            'sunday' => 'minggu'
        ];
        
        $mappedDay = $dayMapping[$dayName] ?? $dayName;
        $todaySchedule = $guru->weeklySchedules()->where('hari', $mappedDay)->first();
        
        if (!$todaySchedule) {
            if ($photoPath) {
                AbsensiPhoto::create([
                    'guru_id' => $guru->id,
                    'absensi_id' => null,
                    'uid' => $uid,
                    'photo_path' => $photoPath,
                    'capture_type' => 'failed_attempt',
                    'capture_context' => 'Jadwal untuk hari ini belum diatur',
                    'captured_at' => $photoCapturedAt
                ]);
            }
            
            \Log::warning("No schedule found for guru", [
                'guru_id' => $guru->id,
                'day' => $mappedDay,
                'original_day' => $dayName
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Jadwal untuk hari ini belum diatur. Silakan hubungi administrator.'
            ]);
        }
        
        if (!$todaySchedule->is_active) {
            if ($photoPath) {
                AbsensiPhoto::create([
                    'guru_id' => $guru->id,
                    'absensi_id' => null,
                    'uid' => $uid,
                    'photo_path' => $photoPath,
                    'capture_type' => 'failed_attempt',
                    'capture_context' => 'Hari libur - tidak perlu absensi',
                    'captured_at' => $photoCapturedAt
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Hari ini adalah hari libur. Anda tidak perlu melakukan absensi.'
            ]);
        }
        
        // Cek apakah guru sudah absen hari ini
$existingAbsen = Absensi::where('guru_id', $guru->id)
    ->whereDate('tanggal', $today)
    ->first();

if ($existingAbsen) {
    // === SUDAH PERNAH ABSEN MASUK ===
    if (is_null($existingAbsen->jam_keluar)) {
        // Belum ada jam keluar → berarti tap ke-2 (absen pulang)
        $scheduledExitTime = Carbon::createFromFormat('H:i:s', $todaySchedule->jam_pulang, 'Asia/Jakarta');
        $scheduledExitTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);

        // Cegah pulang terlalu cepat (>30 menit sebelum jadwal)
        if ($currentTime->lt($scheduledExitTime->copy()->subMinutes(30))) {
            return response()->json([
                'success' => false,
                'message' => 'Belum waktunya pulang. Jam pulang: ' . $todaySchedule->jam_pulang
            ]);
        }

        // Update data absensi untuk jam keluar
        $existingAbsen->update([
            'jam_keluar' => $currentTime->format('H:i:s'),
            'status_keluar' => 'normal',
            'status_verifikasi' => $verification['status'],
            'catatan_verifikasi' => $verification['catatan']
        ]);

        // Simpan foto kalau ada
        if (!empty($photoPath)) {
            AbsensiPhoto::create([
                'guru_id' => $guru->id,
                'absensi_id' => $existingAbsen->id,
                'uid' => $uid,
                'photo_path' => $photoPath,
                'capture_type' => 'keluar',
                'capture_context' => 'Absensi pulang berhasil - ' . $verification['catatan'],
                'captured_at' => $photoCapturedAt
            ]);
        }

        return response()->json([
            'success' => true,
            'type' => 'keluar',
            'message' => "Absensi pulang berhasil! Selamat beristirahat, {$guru->nama}.",
            'data' => [
                'nama' => $guru->nama,
                'jabatan' => $guru->jabatan,
                'jam_masuk' => $currentTime->format('H:i'), // 🔁 ganti key-nya
                'tanggal' => $today->format('d F Y'),
                'status' => 'NORMAL',
                'verification' => $verification
            ]
        ]);
    } else {
        // === SUDAH ABSEN MASUK & KELUAR ===
        return response()->json([
            'success' => false,
            'message' => 'Anda sudah absen masuk dan pulang hari ini.'
        ]);
    }
}

        
        if ($existingAbsen) {
            // Jika sudah ada absen masuk, ini adalah absen keluar
            if (!$existingAbsen->jam_keluar) {
                $isManualEntry = in_array($existingAbsen->status_masuk, ['izin', 'sakit']);
                
                if (!$isManualEntry) {
                    $scheduledExitTime = Carbon::createFromFormat('H:i:s', $todaySchedule->jam_pulang, 'Asia/Jakarta');
                    $scheduledExitTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                    
                    if ($currentTime->lt($scheduledExitTime->subMinutes(30))) {
                        if ($photoPath) {
                            AbsensiPhoto::create([
                                'guru_id' => $guru->id,
                                'absensi_id' => $existingAbsen->id,
                                'uid' => $uid,
                                'photo_path' => $photoPath,
                                'capture_type' => 'failed_attempt',
                                'capture_context' => 'Belum waktunya pulang',
                                'captured_at' => $photoCapturedAt
                            ]);
                        }
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Belum waktunya pulang. Jam pulang: ' . $todaySchedule->jam_pulang_formatted
                        ]);
                    }
                }
                
                $updateData = [
                    'jam_keluar' => $currentTime->format('H:i:s'),
                    'status_keluar' => 'normal'
                ];
                
                if ($photoPath) {
                    AbsensiPhoto::create([
                        'guru_id' => $guru->id,
                        'absensi_id' => $existingAbsen->id,
                        'uid' => $uid,
                        'photo_path' => $photoPath,
                        'capture_type' => 'keluar',
                        'capture_context' => 'Absensi pulang berhasil',
                        'captured_at' => $photoCapturedAt
                    ]);
                    
                    // Update juga photo_path lama untuk backward compatibility
                    $updateData['photo_path'] = $photoPath;
                    $updateData['photo_captured_at'] = $photoCapturedAt;
                }
                
                $existingAbsen->update($updateData);
                
                $message = $isManualEntry 
                    ? "Absensi pulang berhasil! Semoga lekas sembuh/urusan selesai, {$guru->nama}"
                    : "Absensi pulang berhasil! Selamat jalan {$guru->nama}";
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'type' => 'keluar',
                    'data' => [
                        'nama' => $guru->nama,
                        'jabatan' => $guru->jabatan,
                        'jam_keluar' => $currentTime->format('H:i'),
                        'tanggal' => $today->format('d F Y'),
                        'status' => 'NORMAL',
                        'has_photo' => !is_null($photoPath)
                    ]
                ]);
            } else {
                if ($photoPath) {
                    AbsensiPhoto::create([
                        'guru_id' => $guru->id,
                        'absensi_id' => $existingAbsen->id,
                        'uid' => $uid,
                        'photo_path' => $photoPath,
                        'capture_type' => 'failed_attempt',
                        'capture_context' => 'Sudah absen masuk dan pulang hari ini',
                        'captured_at' => $photoCapturedAt
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah absen masuk dan pulang hari ini'
                ]);
            }
        }
        
        $manualEntry = Absensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $today)
            ->whereIn('status_masuk', ['izin', 'sakit'])
            ->first();
        
        if ($manualEntry) {
            $updateData = [
                'jam_masuk' => $currentTime->format('H:i:s')
            ];
            
            if ($photoPath) {
                AbsensiPhoto::create([
                    'guru_id' => $guru->id,
                    'absensi_id' => $manualEntry->id,
                    'uid' => $uid,
                    'photo_path' => $photoPath,
                    'capture_type' => 'masuk',
                    'capture_context' => 'Scan kartu untuk entry manual (' . $manualEntry->status_masuk . ')',
                    'captured_at' => $photoCapturedAt
                ]);
                
                $updateData['photo_path'] = $photoPath;
                $updateData['photo_captured_at'] = $photoCapturedAt;
            }
            
            $manualEntry->update($updateData);
            
            $statusLabel = $manualEntry->status_masuk == 'izin' ? 'Izin' : 'Sakit';
            
            return response()->json([
                'success' => true,
                'message' => "Scan berhasil! Status: {$statusLabel}. Semoga lekas sembuh/urusan selesai, {$guru->nama}",
                'type' => 'masuk',
                'data' => [
                    'nama' => $guru->nama,
                    'jabatan' => $guru->jabatan,
                    'jam_masuk' => $currentTime->format('H:i'),
                    'tanggal' => $today->format('d F Y'),
                    'jam_masuk_jadwal' => $todaySchedule->jam_masuk_formatted,
                    'status' => strtoupper($manualEntry->status_masuk),
                    'keterlambatan' => 0,
                    'has_photo' => !is_null($photoPath)
                ]
            ]);
        }
        
        $scheduledEntryTime = Carbon::createFromFormat('H:i:s', $todaySchedule->jam_masuk, 'Asia/Jakarta');
        $scheduledEntryTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
        
        $status = 'HADIR';
        $keterlambatan = 0;
        $message = "Selamat datang {$guru->nama}! Absensi masuk berhasil.";
        
        if ($currentTime->gt($scheduledEntryTime->addMinutes(15))) {
    // Hitung keterlambatan (selisih waktu)
    $diff = $scheduledEntryTime->subMinutes(15)->diff($currentTime);

    // Konversi ke jam dan menit
    $jam = $diff->h;
    $menit = $diff->i;

    // Buat teks keterlambatan
    if ($jam > 0) {
        $keterlambatanText = "{$jam} jam {$menit} menit";
    } else {
        $keterlambatanText = "{$menit} menit";
    }

    // Total keterlambatan dalam menit untuk disimpan di DB
    $keterlambatan = $diff->h * 60 + $diff->i;

    $status = 'TERLAMBAT';
    $message = "Absensi masuk berhasil. Anda terlambat {$keterlambatanText}, {$guru->nama}.";
} else {
    $status = 'HADIR';
    $keterlambatan = 0;
    $message = "Selamat datang {$guru->nama}! Absensi masuk berhasil.";
}
        
        $absensiData = [
            'guru_id' => $guru->id,
            'tanggal' => $today,
            'jam_masuk' => $currentTime->format('H:i:s'),
            'status_masuk' => $status === 'TERLAMBAT' ? 'terlambat' : 'hadir',
            'keterangan_masuk' => $message,
            'keterlambatan' => $keterlambatan,
            'status_verifikasi' => $verification['status'],
            'catatan_verifikasi' => $verification['catatan']
        ];
        
        if ($photoPath) {
            $absensiData['photo_path'] = $photoPath;
            $absensiData['photo_captured_at'] = $photoCapturedAt;
        }
        
        $absensi = Absensi::create($absensiData);
        
        if ($photoPath) {
            AbsensiPhoto::create([
                'guru_id' => $guru->id,
                'absensi_id' => $absensi->id,
                'uid' => $uid,
                'photo_path' => $photoPath,
                'capture_type' => 'masuk',
                'capture_context' => $message . ' - ' . $verification['catatan'],
                'captured_at' => $photoCapturedAt
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'type' => 'masuk',
            'data' => [
                'nama' => $guru->nama,
                'jabatan' => $guru->jabatan,
                'jam_masuk' => $currentTime->format('H:i'),
                'tanggal' => $today->format('d F Y'),
                'jam_masuk_jadwal' => date('H:i', strtotime($todaySchedule->jam_masuk)),
                'status' => $status,
                'keterlambatan' => $keterlambatan,
                'has_photo' => !is_null($photoPath),
                'verification' => $verification
            ]
        ]);
    }

    public function showPhotos($absensiId)
    {
        $absensi = Absensi::with(['guru', 'photos'])->findOrFail($absensiId);
        $allPhotos = $absensi->getAllPhotosForDay();
        
        return response()->json([
            'success' => true,
            'data' => [
                'absensi' => $absensi,
                'guru' => $absensi->guru,
                'photos' => $allPhotos->map(function($photo) {
                    return [
                        'id' => $photo->id,
                        'photo_url' => $photo->photo_url,
                        'captured_at' => $photo->captured_at_formatted,
                        'capture_type' => $photo->capture_type,
                        'capture_context' => $photo->capture_context
                    ];
                })
            ]
        ]);
    }

    public function servePhoto($filename)
    {
        // Validasi filename untuk keamanan
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            abort(404, 'File tidak ditemukan');
        }
        
        $path = storage_path('app/public/absensi_photos/' . $filename);
        
        // Cek apakah file exists
        if (!file_exists($path)) {
            abort(404, 'Foto tidak ditemukan');
        }
        
        // Cek apakah user memiliki akses ke foto ini
        $photo = AbsensiPhoto::where('photo_path', 'absensi_photos/' . $filename)->first();
        
        if (!$photo) {
            // Fallback: cek di tabel absensi untuk backward compatibility
            $absensi = Absensi::where('photo_path', 'absensi_photos/' . $filename)->first();
            if (!$absensi) {
                abort(404, 'Foto tidak ditemukan dalam sistem');
            }
        }
        
        // Return file dengan headers yang tepat
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Verifikasi kesesuaian wajah guru antara database dan hasil deteksi kamera
     */
    private function verifyFace($guru, $currentFaceDescriptorJson)
    {
        if (!$guru || empty($guru->face_descriptor)) {
            return [
                'status' => 'belum_didaftarkan',
                'catatan' => 'Guru belum mendaftarkan data wajah',
                'similarity' => null,
                'is_verified' => false
            ];
        }

        if (empty($currentFaceDescriptorJson)) {
            return [
                'status' => 'gagal',
                'catatan' => 'Wajah tidak terdeteksi oleh kamera saat scan kartu',
                'similarity' => 0,
                'is_verified' => false
            ];
        }

        $registered = json_decode($guru->face_descriptor, true);
        $current = json_decode($currentFaceDescriptorJson, true);

        if (!is_array($registered) || !is_array($current)) {
            return [
                'status' => 'gagal',
                'catatan' => 'Format data fitur wajah tidak valid',
                'similarity' => null,
                'is_verified' => false
            ];
        }

        // Hitung Euclidean Distance antar vektor 128 dimensi
        $sum = 0.0;
        $count = min(count($registered), count($current));
        for ($i = 0; $i < $count; $i++) {
            $diff = (float)$registered[$i] - (float)$current[$i];
            $sum += $diff * $diff;
        }
        $distance = sqrt($sum);

        // Ambang batas Euclidean distance (0.50 standar face-api.js)
        $threshold = 0.50;
        $similarity = max(0, min(100, (1 - ($distance / 1.0)) * 100));
        $similarity = round($similarity, 1);

        if ($distance <= $threshold) {
            return [
                'status' => 'terverifikasi',
                'catatan' => "Wajah cocok ({$similarity}%, Jarak: " . round($distance, 3) . ")",
                'similarity' => $similarity,
                'is_verified' => true
            ];
        } else {
            return [
                'status' => 'gagal',
                'catatan' => "Wajah tidak cocok dengan pemilik kartu (Kemiripan: {$similarity}%, Jarak: " . round($distance, 3) . ")",
                'similarity' => $similarity,
                'is_verified' => false
            ];
        }
    }
}
