<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dudi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'bidang_usaha',
        'alamat',
        'kota',
        'kontak_person',
        'no_hp',
        'email',
        'kuota',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}