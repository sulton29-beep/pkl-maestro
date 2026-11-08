<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'is_arsip',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'is_active'       => 'boolean',
        'is_arsip'        => 'boolean',
    ];

    /* ==========================================================
     * RELASI
     * ========================================================== */

    public function penempatanPkls(): HasMany
    {
        return $this->hasMany(PenempatanPkl::class, 'tahun_ajaran_id');
    }

    /* ==========================================================
     * HELPER STATIC
     * ========================================================== */

    public static function getAktif(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public static function getAktifId(): ?int
    {
        $aktif = static::getAktif();
        if ($aktif) {
            return $aktif->id;
        }

        return static::orderBy('tanggal_mulai', 'desc')->value('id');
    }

    public function isArsip(): bool
    {
        return (bool) $this->is_arsip;
    }
}