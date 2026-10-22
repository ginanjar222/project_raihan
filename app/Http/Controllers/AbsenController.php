<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function scanPage()
    {
        return view('scan');
    }

    public function processScan(Request $request)
    {
        try {
            $uid = trim($request->input('uid'));
            
            // Validasi UID
            if (empty($uid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'UID tidak valid atau kosong'
                ]);
            }

            // Log untuk debugging
            \Log::info('UID yang diterima: ' . $uid);

            // Cari guru berdasarkan UID
            $guru = Guru::where('uid', $uid)->first();
            
            if (!$guru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu tidak terdaftar! UID: ' . $uid
                ]);
            }

            // Cek apakah sudah absen hari ini
            $today = Carbon::today();
            $existingAbsensi = Absensi::where('guru_id', $guru->id)
                                     ->whereDate('tanggal', $today)
                                     ->first();

            if ($existingAbsensi) {
                return response()->json([
                    'success' => false,
                    'message' => $guru->nama . ' sudah absen hari ini pada ' . $existingAbsensi->jam_masuk
                ]);
            }

            // Tentukan status berdasarkan jam masuk
            $jamSekarang = Carbon::now();
            $jamMasukGuru = Carbon::createFromFormat('H:i:s', $guru->jam_masuk);
            $status = $jamSekarang->gt($jamMasukGuru->addMinutes(15)) ? 'terlambat' : 'hadir';

            // Buat record absensi baru
            $absensi = Absensi::create([
                'guru_id' => $guru->id,
                'tanggal' => $today,
                'jam_masuk' => $jamSekarang->format('H:i:s'),
                'status' => $status
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Absensi Berhasil! Selamat datang ' . $guru->nama,
                'data' => [
                    'nama' => $guru->nama,
                    'jabatan' => $guru->jabatan,
                    'jam_masuk' => $absensi->jam_masuk,
                    'tanggal' => $absensi->tanggal->format('d/m/Y'),
                    'status' => $status
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error dalam processScan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    public function getScanStatus()
    {
        return response()->json([
            'success' => false,
            'message' => 'Menunggu Scan Kartu...'
        ]);
    }
}
