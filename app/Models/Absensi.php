<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id', 'dudi_id', 'tanggal',
        'jam_masuk', 'jam_keluar',
        'foto_masuk', 'foto_keluar',
        'latitude_masuk', 'longitude_masuk',
        'latitude_keluar', 'longitude_keluar',
        'jarak_masuk', 'jarak_keluar',
        'status_masuk', 'status_keluar', 'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }
}