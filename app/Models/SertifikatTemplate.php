<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SertifikatTemplate extends Model
{
    use HasFactory;

    protected $table = 'sertifikat_templates';

    protected $fillable = [
        'nama',
        'jurusan_id',
        'gambar_background',
        'warna_teks',
        'warna_aksen',
        'is_active',
        'catatan_admin',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Ambil template aktif untuk jurusan tertentu.
     * Prioritas:
     *   1. Template aktif yang match jurusan_id
     *   2. Template aktif default (jurusan_id = NULL)
     *   3. null (biar fallback ke desain blade default)
     */
    public static function getForJurusan(?int $jurusanId): ?self
    {
        // 1) Cari template yang khusus jurusan ini
        if ($jurusanId) {
            $template = self::where('jurusan_id', $jurusanId)
                ->where('is_active', true)
                ->latest()
                ->first();

            if ($template) {
                return $template;
            }
        }

        // 2) Fallback ke template default (jurusan_id NULL)
        return self::whereNull('jurusan_id')
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}