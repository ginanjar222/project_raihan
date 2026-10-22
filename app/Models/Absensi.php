<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'guru_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_masuk',
        'status_keluar',
        'status_verifikasi',
        'catatan_verifikasi',
        'keterangan_masuk',
        'keterangan_keluar',
        'keterlambatan', // Menambahkan field keterlambatan ke fillable
        'is_manual',
        'created_by',
        'photo_path',
        'photo_captured_at',
        'evidence_file' // Adding evidence_file field for manual attendance proof
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
        'is_manual' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'photo_captured_at' => 'datetime'
    ];

    const STATUS_MASUK = [
        'hadir' => 'Hadir',
        'terlambat' => 'Terlambat',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpa' => 'Alpa'
    ];

    const STATUS_KELUAR = [
        'normal' => 'Normal',
        'cepat' => 'Pulang Cepat',
        'izin' => 'Izin',
        'sakit' => 'Sakit'
    ];

    /**
     * Relasi ke model Guru
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function photos()
    {
        return $this->hasMany(AbsensiPhoto::class);
    }

    public function getAllPhotosForDay()
    {
        return AbsensiPhoto::where('guru_id', $this->guru_id)
            ->whereDate('captured_at', $this->tanggal)
            ->orderBy('captured_at', 'desc')
            ->get();
    }

    public function getStatusMasukLabelAttribute()
    {
        return self::STATUS_MASUK[$this->status_masuk] ?? 'Tidak Diketahui';
    }

    public function getStatusKeluarLabelAttribute()
    {
        return $this->status_keluar ? (self::STATUS_KELUAR[$this->status_keluar] ?? 'Tidak Diketahui') : '-';
    }

    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'hadir' => 'bg-success',
            'terlambat' => 'bg-warning',
            'izin' => 'bg-info',
            'sakit' => 'bg-secondary',
            'alpa' => 'bg-danger'
        ];
        
        return $classes[$this->status_masuk] ?? 'bg-secondary';
    }

    public function canEdit()
    {
        // Bisa edit jika input manual atau dalam 24 jam terakhir
        return $this->is_manual || $this->created_at->diffInHours(now()) <= 24;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_masuk', $status);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return null;
    }

    public function hasPhoto()
    {
        return !is_null($this->photo_path) && \Storage::disk('public')->exists($this->photo_path);
    }

    public function hasPhotos()
    {
        return $this->photos()->count() > 0 || $this->hasPhoto();
    }

    public function getPhotosCount()
    {
        return $this->getAllPhotosForDay()->count();
    }

    public function hasEvidence()
    {
        return !is_null($this->evidence_file) && \Storage::disk('public')->exists($this->evidence_file);
    }

    public function getEvidenceUrlAttribute()
    {
        if ($this->evidence_file) {
            return asset('storage/' . $this->evidence_file);
        }
        return null;
    }
}
