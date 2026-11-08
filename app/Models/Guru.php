<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'gurus';

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'jenis_kelamin',
        'email',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penempatanSebagaiPembimbing()
    {
        return $this->hasMany(PenempatanPkl::class, 'guru_pembimbing_id');
    }

    public function penempatanSebagaiPenguji()
    {
        return $this->hasMany(PenempatanPkl::class, 'guru_penguji_id');
    }
}