<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function scanPage()
    {
        return view('scan');
    }

    public function processScan(Request $request)
    {
        try {
            // Log semua data yang masuk untuk debugging
            Log::info('Request data:', $request->all());

            // Validasi request
            $request->validate([
                'uid' => 'required|string'
            ]);

            $uid = trim($request->input('uid'));
            
            // Log untuk debugging
            Log::info('UID yang diterima: ' . $uid);

            // Cari guru berdasarkan UID
            $guru = Guru::where('uid', $uid)->first();
            
            if (!$guru) {
                Log::warning('UID tidak ditemukan: ' . $uid);
                
                // Log semua UID yang ada untuk debugging
                $allUIDs = Guru::pluck('uid', 'nama')->toArray();
                Log::info('All UIDs in database:', $allUIDs);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu tidak terdaftar! UID: ' . $uid
                ]);
            }

            Log::info('Guru ditemukan:', ['nama' => $guru->nama, 'uid' => $guru->uid]);

            // Cek apakah sudah absen hari ini
            $today = Carbon::today();
            $existingAbsensi = Absensi::where('guru_id', $guru->id)
                                     ->whereDate('tanggal', $today)
                                     ->first();

            if ($existingAbsensi) {
                Log::info('Guru sudah absen: ' . $guru->nama);
                return response()->json([
                    'success' => false,
                    'message' => $guru->nama . ' sudah absen hari ini pada ' . Carbon::parse($existingAbsensi->jam_masuk)->format('H:i'),
                    'data' => [
                        'nama' => $guru->nama,
                        'jabatan' => $guru->jabatan,
                        'jam_masuk' => Carbon::parse($existingAbsensi->jam_masuk)->format('H:i'),
                        'tanggal' => $existingAbsensi->tanggal->format('d/m/Y'),
                        'status' => ucfirst($existingAbsensi->status_masuk ?? $existingAbsensi->status)
                    ]
                ]);
            }

            // Tentukan status berdasarkan jam masuk
            $jamSekarang = Carbon::now();
            $jamMasukGuru = Carbon::createFromFormat('H:i:s', $guru->jam_masuk);
            
            // Clone untuk perbandingan (agar tidak mengubah jam asli)
            $batasWaktu = $jamMasukGuru->copy()->addMinutes(15);
            $status = $jamSekarang->gt($batasWaktu) ? 'terlambat' : 'hadir';

            // Log sebelum create
            Log::info('Creating absensi with data:', [
                'guru_id' => $guru->id,
                'tanggal' => $today->format('Y-m-d'),
                'jam_masuk' => $jamSekarang->format('H:i:s'),
                'status_masuk' => $status
            ]);

            // Buat record absensi baru
            $absensi = Absensi::create([
                'guru_id' => $guru->id,
                'tanggal' => $today,
                'jam_masuk' => $jamSekarang->format('H:i:s'),
                'status_masuk' => $status,
                'is_manual' => false,
                'created_by' => 'system'
            ]);

            Log::info('Absensi berhasil dibuat:', ['id' => $absensi->id]);

            return response()->json([
                'success' => true,
                'message' => '✅ Absensi Berhasil! Selamat datang ' . $guru->nama,
                'data' => [
                    'nama' => $guru->nama,
                    'jabatan' => $guru->jabatan,
                    'jam_masuk' => Carbon::parse($absensi->jam_masuk)->format('H:i'),
                    'tanggal' => $absensi->tanggal->format('d/m/Y'),
                    'status' => ucfirst($status)
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $e->errors()['uid'] ?? ['UID diperlukan'])
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error dalam processScan: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getScanStatus()
    {
        return response()->json([
            'success' => false,
            'message' => 'Menunggu Scan Kartu...'
        ]);
    }

    public function manualForm()
    {
        $guru = Guru::orderBy('nama')->get();
        return view('absensi.manual', compact('guru'));
    }

    public function manualStore(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'guru_id' => 'required|exists:guru,id',
                'tanggal' => 'required|date|before_or_equal:today',
                'status_masuk' => 'required|in:izin,sakit,alpa',
                'jam_masuk' => 'nullable|date_format:H:i',
                'keterangan_masuk' => 'required|string|min:10|max:500',
                'evidence_file' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx'
            ], [
                'guru_id.required' => 'Pilih guru/staff terlebih dahulu',
                'guru_id.exists' => 'Guru/staff tidak ditemukan',
                'tanggal.required' => 'Tanggal harus diisi',
                'tanggal.date' => 'Format tanggal tidak valid',
                'tanggal.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini',
                'status_masuk.required' => 'Status kehadiran harus dipilih',
                'status_masuk.in' => 'Status kehadiran tidak valid',
                'jam_masuk.date_format' => 'Format jam tidak valid (HH:MM)',
                'keterangan_masuk.required' => 'Keterangan harus diisi',
                'keterangan_masuk.min' => 'Keterangan minimal 10 karakter',
                'keterangan_masuk.max' => 'Keterangan maksimal 500 karakter',
                'evidence_file.file' => 'File bukti tidak valid',
                'evidence_file.max' => 'Ukuran file maksimal 2MB',
                'evidence_file.mimes' => 'Format file harus JPG, PNG, PDF, DOC, atau DOCX'
            ]);

            $guru = Guru::findOrFail($request->guru_id);
            $tanggal = Carbon::parse($request->tanggal);

            // Cek apakah sudah ada absensi untuk guru dan tanggal tersebut
            $existingAbsensi = Absensi::where('guru_id', $request->guru_id)
                                     ->whereDate('tanggal', $tanggal)
                                     ->first();

            if ($existingAbsensi) {
                return back()->withErrors([
                    'guru_id' => 'Guru ' . $guru->nama . ' sudah memiliki data absensi pada tanggal ' . $tanggal->format('d/m/Y')
                ])->withInput();
            }

            // Siapkan data untuk disimpan
            $data = [
                'guru_id' => $request->guru_id,
                'tanggal' => $tanggal,
                'status_masuk' => $request->status_masuk,
                'keterangan_masuk' => $request->keterangan_masuk,
                'is_manual' => true,
                'created_by' => 'admin' // Bisa disesuaikan dengan sistem auth
            ];

            if ($request->hasFile('evidence_file')) {
                $file = $request->file('evidence_file');
                $fileName = time() . '_' . $guru->id . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('absensi/evidence', $fileName, 'public');
                $data['evidence_file'] = $filePath;
            }

            // Tambahkan jam masuk jika diisi (untuk izin/sakit yang datang terlambat)
            if ($request->jam_masuk && $request->status_masuk !== 'alpa') {
                $data['jam_masuk'] = $request->jam_masuk . ':00';
            }

            // Simpan data absensi
            $absensi = Absensi::create($data);

            Log::info('Manual absensi created:', [
                'id' => $absensi->id,
                'guru' => $guru->nama,
                'tanggal' => $tanggal->format('Y-m-d'),
                'status' => $request->status_masuk,
                'has_evidence' => $request->hasFile('evidence_file')
            ]);

            return redirect()->route('absensi.manual')
                           ->with('success', 'Data absensi ' . $guru->nama . ' berhasil disimpan dengan status ' . strtoupper($request->status_masuk));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error in manualStore: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()])->withInput();
        }
    }

    public function viewEvidence($id)
    {
        try {
            $absensi = Absensi::with('guru')->findOrFail($id);
            
            if (!$absensi->hasEvidence()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukti tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'evidence_url' => $absensi->evidence_url,
                    'guru_nama' => $absensi->guru->nama,
                    'tanggal' => $absensi->tanggal->format('d/m/Y'),
                    'status' => $absensi->status_masuk_label
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in viewEvidence: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function editKeterangan($id)
    {
        $absensi = Absensi::with('guru')->findOrFail($id);
        
        // Cek apakah bisa diedit
        if (!$absensi->canEdit()) {
            return back()->withErrors(['error' => 'Data absensi ini tidak dapat diedit karena sudah melewati batas waktu']);
        }

        return view('absensi.edit-keterangan', compact('absensi'));
    }

    public function updateKeterangan(Request $request, $id)
    {
        try {
            $absensi = Absensi::findOrFail($id);
            
            // Cek apakah bisa diedit
            if (!$absensi->canEdit()) {
                return back()->withErrors(['error' => 'Data absensi ini tidak dapat diedit karena sudah melewati batas waktu']);
            }

            $request->validate([
                'keterangan_masuk' => 'nullable|string|max:500',
                'keterangan_keluar' => 'nullable|string|max:500'
            ], [
                'keterangan_masuk.max' => 'Keterangan masuk maksimal 500 karakter',
                'keterangan_keluar.max' => 'Keterangan keluar maksimal 500 karakter'
            ]);

            $absensi->update([
                'keterangan_masuk' => $request->keterangan_masuk,
                'keterangan_keluar' => $request->keterangan_keluar
            ]);

            Log::info('Keterangan absensi updated:', [
                'id' => $absensi->id,
                'guru' => $absensi->guru->nama
            ]);

            return back()->with('success', 'Keterangan absensi berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Error in updateKeterangan: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function getDashboardStats()
    {
        $today = Carbon::today();
        
        $stats = [
            'totalGuru' => Guru::count(),
            'sudahAbsen' => Absensi::whereDate('tanggal', $today)->count(),
            'belumAbsen' => Guru::count() - Absensi::whereDate('tanggal', $today)->count(),
            'hadir' => Absensi::whereDate('tanggal', $today)->where('status_masuk', 'hadir')->count(),
            'terlambat' => Absensi::whereDate('tanggal', $today)->where('status_masuk', 'terlambat')->count(),
            'izin' => Absensi::whereDate('tanggal', $today)->where('status_masuk', 'izin')->count(),
            'sakit' => Absensi::whereDate('tanggal', $today)->where('status_masuk', 'sakit')->count(),
            'alpa' => Absensi::whereDate('tanggal', $today)->where('status_masuk', 'alpa')->count(),
            'sudahPulang' => Absensi::whereDate('tanggal', $today)->whereNotNull('jam_keluar')->count(),
            'masihDiSekolah' => Absensi::whereDate('tanggal', $today)->whereNull('jam_keluar')->count()
        ];

        return $stats;
    }

    public function listAbsensi(Request $request)
    {
        $query = Absensi::with('guru');
        
        // Filter berdasarkan tanggal
        if ($request->tanggal_dari) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->tanggal_sampai) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
        // Filter berdasarkan status
        if ($request->status) {
            $query->where('status_masuk', $request->status);
        }
        
        // Default filter bulan ini jika tidak ada filter
        if (!$request->tanggal_dari && !$request->tanggal_sampai) {
            $query->whereMonth('tanggal', now()->month)
                  ->whereYear('tanggal', now()->year);
        }
        
        $absensi = $query->orderBy('tanggal', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
        
        return view('absensi.list', compact('absensi'));
    }

    // Method untuk testing manual
    public function testScan($uid)
    {
        $request = new Request(['uid' => $uid]);
        return $this->processScan($request);
    }

    public function downloadEvidence($id)
    {
        try {
            $absensi = Absensi::with('guru')->findOrFail($id);
            
            if (!$absensi->hasEvidence()) {
                abort(404, 'Bukti tidak ditemukan');
            }

            $filePath = storage_path('app/public/' . $absensi->evidence_file);
            
            if (!file_exists($filePath)) {
                abort(404, 'File bukti tidak ditemukan di server');
            }

            $fileName = $absensi->guru->nama . '_' . $absensi->tanggal->format('Y-m-d') . '_bukti.' . pathinfo($filePath, PATHINFO_EXTENSION);
            
            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            Log::error('Error in downloadEvidence: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat mengunduh file');
        }
    }

    public function evidenceList(Request $request)
    {
        $query = Absensi::with('guru')->whereNotNull('evidence_file');
        
        // Filter berdasarkan tanggal
        if ($request->tanggal_dari) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->tanggal_sampai) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
        // Filter berdasarkan guru
        if ($request->guru_id) {
            $query->where('guru_id', $request->guru_id);
        }
        
        // Filter berdasarkan status
        if ($request->status) {
            $query->where('status_masuk', $request->status);
        }
        
        // Default filter bulan ini jika tidak ada filter
        if (!$request->tanggal_dari && !$request->tanggal_sampai) {
            $query->whereMonth('tanggal', now()->month)
                  ->whereYear('tanggal', now()->year);
        }
        
        $evidences = $query->orderBy('tanggal', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);
        
        $guru = Guru::orderBy('nama')->get();
        
        return view('absensi.evidence-list', compact('evidences', 'guru'));
    }

    public function deleteEvidence($id)
    {
        try {
            $absensi = Absensi::findOrFail($id);
            
            if (!$absensi->hasEvidence()) {
                return back()->withErrors(['error' => 'Bukti tidak ditemukan']);
            }

            // Hapus file dari storage
            $filePath = storage_path('app/public/' . $absensi->evidence_file);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update database
            $absensi->update(['evidence_file' => null]);

            Log::info('Evidence deleted:', [
                'absensi_id' => $absensi->id,
                'guru' => $absensi->guru->nama,
                'file' => $absensi->evidence_file
            ]);

            return back()->with('success', 'Bukti berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error in deleteEvidence: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus bukti']);
        }
    }
}
