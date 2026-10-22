<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklySchedule extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'guru_id',
        'hari',
        'is_active',
        'jam_masuk',
        'jam_pulang'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'jam_masuk' => 'string',
        'jam_pulang' => 'string',
    ];
    
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
    
    public function getJamMasukFormattedAttribute()
    {
        return $this->jam_masuk ? date('H:i', strtotime($this->jam_masuk)) : '07:00';
    }
    
    public function getJamPulangFormattedAttribute()
    {
        return $this->jam_pulang ? date('H:i', strtotime($this->jam_pulang)) : '15:30';
    }
    
    public static function getHariOptions()
    {
        return [
            'senin' => 'Senin',
            'selasa' => 'Selasa', 
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu'
        ];
    }
}
