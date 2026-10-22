<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    
    protected $table = 'guru';
    
    protected $fillable = [
        'nip', // menambahkan field NIP ke fillable
        'nama',
        'jabatan', 
        'foto',
        'face_descriptor',
        'uid',
        'jam_masuk',
        'jam_pulang'
    ];
    
    
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
    
    public function weeklySchedules()
    {
        return $this->hasMany(WeeklySchedule::class);
    }
    
    public function getJamMasukAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }
    
    public function getJamPulangAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : '16:00';
    }
    
    public function getFotoUrlAttribute()
    {
        if ($this->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto)) {
            return asset('storage/' . $this->foto);
        }
        return null;
    }

    public function getHasFaceAttribute()
    {
        return !empty($this->face_descriptor);
    }
}
