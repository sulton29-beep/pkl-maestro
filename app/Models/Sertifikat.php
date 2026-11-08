<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikats';

    protected $fillable = [
        'penempatan_pkl_id',
        'siswa_id',
        'nomor_sertifikat',
        'qr_token',
        'status',
        'catatan_revisi',
        'tanggal_terbit',
        'ttd_kepsek',
        'ttd_dudi',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_VALIDASI_KAPRODI = 'divalidasi_kaprodi';
    public const STATUS_DISETUJUI_WAKA = 'disetujui_waka';
    public const STATUS_TERBIT = 'terbit';
    public const STATUS_DITOLAK = 'ditolak';

    public static function statusList(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_VALIDASI_KAPRODI => 'Divalidasi Kaprodi',
            self::STATUS_DISETUJUI_WAKA => 'Disetujui Waka',
            self::STATUS_TERBIT => 'Terbit',
            self::STATUS_DITOLAK => 'Ditolak',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusList()[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_VALIDASI_KAPRODI => 'warning',
            self::STATUS_DISETUJUI_WAKA => 'info',
            self::STATUS_TERBIT => 'success',
            self::STATUS_DITOLAK => 'danger',
            default => 'gray',
        };
    }

    public function isTerbit(): bool
    {
        return $this->status === self::STATUS_TERBIT;
    }

    public function bisaDidownload(): bool
    {
        return $this->isTerbit();
    }

    public function penempatanPkl(): BelongsTo
    {
        return $this->belongsTo(PenempatanPkl::class, 'penempatan_pkl_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}