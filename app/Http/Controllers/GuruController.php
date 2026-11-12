<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\WeeklySchedule;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::orderBy('nama')->get();
        return view('guru.index', compact('guru'));
    }
    
    public function create()
    {
        return view('guru.create');
    }
    public function store(Request $request)
    {
        $fotoRule = extension_loaded('fileinfo') 
            ? 'nullable|image|mimes:jpeg,jpg,png|max:5120' 
            : 'nullable|max:5120';

        $request->validate([
            'nip' => 'required|string|unique:guru,nip',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'uid' => 'required|string|unique:guru,uid',
            'foto' => $fotoRule,
            'face_descriptor' => 'nullable|string',
            'schedule' => 'required|array'
        ], [
            'nip.unique' => 'NIP sudah ada, silakan gunakan NIP yang berbeda.',
            'uid.unique' => 'UID sudah ada, silakan gunakan UID yang berbeda.',
            'nip.required' => 'NIP wajib diisi.',
            'uid.required' => 'UID wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'schedule.required' => 'Jadwal mingguan wajib diisi.'
        ]);
        
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = 'guru_' . time() . '_' . uniqid() . '.' . $ext;
            $fotoPath = $file->storeAs('guru_photos', $fileName, 'public');
        }

        $guru = Guru::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'foto' => $fotoPath,
            'face_descriptor' => $request->face_descriptor,
            'uid' => $request->uid,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:30:00'
        ]);
        
        if ($request->has('schedule')) {
            $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            foreach ($days as $day) {
                $scheduleData = $request->schedule[$day] ?? [];
                
                // Only active if checkbox is explicitly checked (active field exists and equals 'on')
                $isActive = isset($scheduleData['active']) && $scheduleData['active'] === 'on';
                $jamMasuk = isset($scheduleData['jam_masuk']) ? $scheduleData['jam_masuk'] . ':00' : '07:00:00';
                $jamPulang = isset($scheduleData['jam_pulang']) ? $scheduleData['jam_pulang'] . ':00' : ($day === 'jumat' ? '11:30:00' : '15:30:00');
                
                WeeklySchedule::create([
                    'guru_id' => $guru->id,
                    'hari' => $day,
                    'is_active' => $isActive,
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang
                ]);
            }
        }
        
        // Log untuk debugging
        \Log::info("Creating Guru with Weekly Schedule and Face:", [
            'guru_id' => $guru->id,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'has_foto' => !empty($fotoPath),
            'has_face_descriptor' => !empty($request->face_descriptor)
        ]);
        
        return redirect()->route('guru.index')
            ->with('success', 'Data guru dan wajah berhasil ditambahkan');
    }
    
    public function edit($id)
    {
        $guru = Guru::with('weeklySchedules')->findOrFail($id);
        return view('guru.edit', compact('guru'));
    }
    
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        
        $fotoRule = extension_loaded('fileinfo') 
            ? 'nullable|image|mimes:jpeg,jpg,png|max:5120' 
            : 'nullable|max:5120';

        $request->validate([
            'nip' => 'required|string|unique:guru,nip,' . $id,
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'uid' => 'required|string|unique:guru,uid,' . $id,
            'foto' => $fotoRule,
            'face_descriptor' => 'nullable|string'
        ], [
            'nip.unique' => 'NIP sudah ada, silakan gunakan NIP yang berbeda.',
            'uid.unique' => 'UID sudah ada, silakan gunakan UID yang berbeda.',
            'nip.required' => 'NIP wajib diisi.',
            'uid.required' => 'UID wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.'
        ]);
        
        $updateData = [
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'uid' => $request->uid
        ];
        
        if ($request->hasFile('foto')) {
            if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
                Storage::disk('public')->delete($guru->foto);
            }
            $file = $request->file('foto');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = 'guru_' . time() . '_' . uniqid() . '.' . $ext;
            $updateData['foto'] = $file->storeAs('guru_photos', $fileName, 'public');
        }
        
        if ($request->filled('face_descriptor')) {
            $updateData['face_descriptor'] = $request->face_descriptor;
        }
        
        $guru->update($updateData);
        
        if ($request->has('schedule')) {
            // Delete existing schedules
            WeeklySchedule::where('guru_id', $guru->id)->delete();
            
            // Create new schedules for all days
            $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            foreach ($days as $day) {
                $scheduleData = $request->schedule[$day] ?? [];
                
                // Check if checkbox is checked (active field exists and equals 'on')
                $isActive = isset($scheduleData['active']) && $scheduleData['active'] === 'on';
                
                // Get time values or use defaults
                $jamMasuk = isset($scheduleData['jam_masuk']) ? $scheduleData['jam_masuk'] . ':00' : '07:00:00';
                $jamPulang = isset($scheduleData['jam_pulang']) ? $scheduleData['jam_pulang'] . ':00' : ($day === 'jumat' ? '11:30:00' : '15:30:00');
                
                WeeklySchedule::create([
                    'guru_id' => $guru->id,
                    'hari' => $day,
                    'is_active' => $isActive,
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang
                ]);
            }
            
            // Log untuk debugging
            \Log::info("Updated Weekly Schedule for Guru:", [
                'guru_id' => $guru->id,
                'nama' => $guru->nama,
                'schedule_data' => $request->schedule
            ]);
        }
        
        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui');
    }
    
    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();
        
        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus');
    }
    
    public function weeklySchedule($id)
    {
        $guru = Guru::with('weeklySchedules')->findOrFail($id);
        
        // Create default schedules if they don't exist
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        foreach ($days as $day) {
            $existingSchedule = $guru->weeklySchedules()->where('hari', $day)->first();
            if (!$existingSchedule) {
                $isActive = in_array($day, ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
                WeeklySchedule::create([
                    'guru_id' => $guru->id,
                    'hari' => $day,
                    'is_active' => $isActive,
                    'jam_masuk' => '07:00:00',
                    'jam_pulang' => '15:30:00'
                ]);
            }
        }
        
        // Reload with fresh schedules
        $guru = Guru::with('weeklySchedules')->findOrFail($id);
        
        return view('guru.weekly-schedule', compact('guru'));
    }
    
    public function updateWeeklySchedule(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'schedules.*.is_active' => 'boolean',
            'schedules.*.jam_masuk' => 'required|date_format:H:i',
            'schedules.*.jam_pulang' => 'required|date_format:H:i'
        ]);
        
        foreach ($request->schedules as $scheduleData) {
            WeeklySchedule::updateOrCreate(
                [
                    'guru_id' => $guru->id,
                    'hari' => $scheduleData['hari']
                ],
                [
                    'is_active' => isset($scheduleData['is_active']) ? true : false,
                    'jam_masuk' => $scheduleData['jam_masuk'] . ':00',
                    'jam_pulang' => $scheduleData['jam_pulang'] . ':00'
                ]
            );
        }
        
        return redirect()->route('guru.weekly-schedule', $id)
            ->with('success', 'Jadwal mingguan berhasil diperbarui');
    }
}
