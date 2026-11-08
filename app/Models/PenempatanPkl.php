<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenempatanPkl extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penempatan_pkls';

    protected $fillable = [
        'tahun_ajaran_id',
        'siswa_id',
        'dudi_id',
        'guru_pembimbing_id',
        'guru_penguji_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /* ==========================================================
     * RELASI
     * ========================================================== */

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }

    public function guruPembimbing()
    {
        return $this->belongsTo(Guru::class, 'guru_pembimbing_id');
    }

    public function guruPenguji()
    {
        return $this->belongsTo(Guru::class, 'guru_penguji_id');
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class, 'penempatan_pkl_id');
    }

    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class, 'penempatan_pkl_id');
    }
}