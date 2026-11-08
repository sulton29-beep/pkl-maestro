<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'aksi',
        'model',
        'model_id',
        'deskripsi',
        'data_lama',
        'data_baru',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    /* ==========================================================
     * RELASI
     * ========================================================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ==========================================================
     * ACCESSOR
     * ========================================================== */

    /**
     * Nama model dalam bahasa Indonesia (dari class path).
     * App\Models\Siswa → "Siswa"
     */
    public function getModelShortNameAttribute(): string
    {
        if (! $this->model) {
            return '-';
        }

        $parts = explode('\\', $this->model);
        return end($parts);
    }

    /**
     * Badge color per aksi.
     */
    public function getAksiColorAttribute(): string
    {
        return match ($this->aksi) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'login'   => 'info',
            'logout'  => 'gray',
            default   => 'primary',
        };
    }
}