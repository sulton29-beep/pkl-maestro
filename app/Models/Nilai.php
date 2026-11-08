<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilais';

    protected $fillable = [
        'penempatan_pkl_id',
        'siswa_id',
        'nilai_kinerja',
        'nilai_kompetensi',
        'nilai_laporan',
        'nilai_presentasi',
        'nilai_sidang',
        'nilai_akhir',
        'catatan_dudi',
        'catatan_guru',
        'catatan_penguji',
    ];

    protected $casts = [
        'nilai_kinerja'    => 'decimal:2',
        'nilai_kompetensi' => 'decimal:2',
        'nilai_laporan'    => 'decimal:2',
        'nilai_presentasi' => 'decimal:2',
        'nilai_sidang'     => 'decimal:2',
        'nilai_akhir'      => 'decimal:2',
    ];

    public function penempatanPkl()
    {
        return $this->belongsTo(PenempatanPkl::class, 'penempatan_pkl_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}