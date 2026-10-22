<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AbsensiPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'absensi_id', 
        'uid',
        'photo_path',
        'capture_type',
        'capture_context',
        'captured_at'
    ];

    protected $casts = [
        'captured_at' => 'datetime'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }
        
        $filename = basename($this->photo_path);
        return route('absensi.photo.serve', ['filename' => $filename]);
    }

    public function getCapturedAtFormattedAttribute()
    {
        return $this->captured_at ? $this->captured_at->format('d/m/Y H:i:s') : null;
    }
}
