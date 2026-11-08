<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
    ];

    /**
     * Relasi ke Siswa
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * Relasi ke SertifikatTemplate (Fase I)
     */
    public function sertifikatTemplates()
    {
        return $this->hasMany(SertifikatTemplate::class, 'jurusan_id');
    }
}