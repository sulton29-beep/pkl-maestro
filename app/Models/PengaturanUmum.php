<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PengaturanUmum extends Model
{
    protected $table = 'pengaturan_umums';

    protected $fillable = [
        'nama_sekolah',
        'npsn',
        'alamat_sekolah',
        'telepon',
        'email',
        'website',
        'nama_aplikasi',
        'logo',
        'favicon',
        'warna_utama',
        'warna_sekunder',
        'meta_deskripsi',
        'meta_keywords',
        'footer_text',
    ];

    /**
     * Ambil baris setting (singleton).
     */
    public static function get(): ?self
    {
        return static::first();
    }

    /**
     * Ambil nilai spesifik dari setting.
     */
    public static function val(string $key, $default = null)
    {
        $row = static::first();
        return $row?->{$key} ?? $default;
    }

    /**
     * URL logo siap pakai.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }
        return Storage::disk('public')->url($this->logo);
    }

    /**
     * URL favicon siap pakai.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if (! $this->favicon) {
            return null;
        }
        return Storage::disk('public')->url($this->favicon);
    }
}