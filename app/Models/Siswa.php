<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'jurusan_id',
        'nis',
        'nisn',
        'nama',
        'jenis_kelamin',
        'kelas',
        'email',
        'no_hp',
        'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class);
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class);
    }

    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class, 'siswa_id');
    }
}